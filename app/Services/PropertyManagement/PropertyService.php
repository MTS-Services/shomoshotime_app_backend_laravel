<?php

namespace App\Services\PropertyManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Area;
use App\Models\Category;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\PropertyView;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    use FileManagementTrait;

    public function getProperties($orderBy = 'sort_order', $order = 'asc')
    {
        return Property::orderBy($orderBy, $order)->latest();
    }

    public function getProperty(string $encryptedValue, $value = 'id')
    {
        return Property::where($value, decrypt($encryptedValue));
    }

    public function getRenewProperty(string $encryptedId): Property|Collection
    {
        return Property::withTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function getPropertyBySlug(string $slug)
    {
        return Property::where('slug', $slug);
    }

    public function getDeletedProperty(string $encryptedId)
    {
        return Property::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function updateViewCount(string $id): PropertyView|bool
    {
        $property = $this->getProperty($id)->first();
        if (!$property) {
            return false;
        }
        return PropertyView::create([
            'property_id' => $property->id,
            'ip_address' => request()->ip()
        ]);
    }

    public function createProperty(array $data): Property
    {
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['status'] = $data['status'] ?? Property::STATUS_OPEN;
        $data['title'] = $data['title'] ?? titleGenerator($data['property_type_id'], $data['category_id'], $data['area_id']);
        $property = Property::create($data);
        return $property;
    }


    public function updateProperty(Property $property, array $data): Property
    {
        $data['updated_by'] = Auth::id();
        $data['title'] = $data['title'] ?? titleGenerator($data['property_type_id'], $data['category_id'], $data['area_id']);
        $property->update($data);
        return $property;
    }


    public function imageCreate(Property $property, UploadedFile $file, array $files = [])
    {

        $userId = Auth::id();

        $data['created_by']  = $userId;
        $data['property_id'] = $property->id;

        if (is_null($property->updated_by)) {
            $property->update(['updated_by' => $userId]);
        }

        // Handle main file (image/video)
        if ($file instanceof UploadedFile) {
            // Delete existing entries
            $existingItems = PropertyImage::where('property_id', $property->id)->get();
            foreach ($existingItems as $item) {
                $this->fileDelete($item->image);
                $item->forceDelete();
            }

            $data['file'] = $this->handleFileUpload($file, 'properties');
            $data['type'] = $this->detectFileType($file); // returns 1, 2, or 0

            PropertyImage::create(array_merge($data, [
                'is_primary' => PropertyImage::PRIMARY,
            ]));
        }

        // Handle gallery files
        foreach ($files as $upload) {
            if ($upload instanceof UploadedFile) {
                $galleryData = $data;
                $galleryData['file']       = $this->handleFileUpload($upload, 'properties');
                $galleryData['type']       = $this->detectFileType($upload);
                $galleryData['is_primary'] = PropertyImage::NOT_PRIMARY;

                PropertyImage::create($galleryData);
            }
        }
    }
    public function imageUpdate(Property $property, ?UploadedFile $file = null, array $files = [])
    {
        return DB::transaction(function () use ($file, $files, $property) {
            $userId = Auth::id();

            $data['updated_by'] = $userId;
            $data['property_id'] = $property->id;

            $property->update(['updated_by' => $userId]);

            // ====== ✅ Update primary file if provided ======
            if ($file instanceof UploadedFile) {
                // Delete existing primary file
                $existingPrimary = PropertyImage::where('property_id', $property->id)
                    ->where('is_primary', PropertyImage::PRIMARY)
                    ->first();

                if ($existingPrimary) {
                    $this->fileDelete($existingPrimary->file);
                    $existingPrimary->forceDelete();
                }

                $data['file'] = $this->handleFileUpload($file, 'properties');
                $data['type'] = $this->detectFileType($file);
                $data['is_primary'] = PropertyImage::PRIMARY;
                PropertyImage::create($data);
            }

            // ====== ✅ Add new gallery files if provided ======
            foreach ($files as $upload) {
                if ($upload instanceof UploadedFile) {
                    $galleryData = $data;
                    $galleryData['file'] = $this->handleFileUpload($upload, 'properties');
                    $galleryData['type'] = $this->detectFileType($upload);
                    $galleryData['is_primary'] = PropertyImage::NOT_PRIMARY;
                    PropertyImage::create($galleryData);
                }
            }
        });
    }

    public function syncPropertyImages(
        Property $property,
        ?UploadedFile $primaryFile = null,
        array $galleryFiles = []
        // removed $deletedGalleryImageIds as it's no longer needed for this logic
    ): void {
        DB::transaction(function () use ($property, $primaryFile, $galleryFiles) {
            $userId = Auth::id();
            $property->update(['updated_by' => $userId]);
            if ($primaryFile instanceof UploadedFile) {
                $existingPrimary = PropertyImage::where('property_id', $property->id)
                    ->where('is_primary', PropertyImage::PRIMARY)
                    ->first();

                if ($existingPrimary) {
                    $this->fileDelete($existingPrimary->file);
                    $existingPrimary->forceDelete();
                }

                PropertyImage::create([
                    'property_id' => $property->id,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'file' => $this->handleFileUpload($primaryFile, 'properties'),
                    'type' => $this->detectFileType($primaryFile),
                    'is_primary' => PropertyImage::PRIMARY,
                ]);
            }

            // ====== 2. Handle Gallery Files (Delete ALL existing non-primary if new ones are provided) ======
            if (!empty($galleryFiles)) {
                $existingGalleryImages = PropertyImage::where('property_id', $property->id)
                    ->where('is_primary', PropertyImage::NOT_PRIMARY)
                    ->get();

                // Delete each existing gallery image from storage and database
                foreach ($existingGalleryImages as $image) {
                    $this->fileDelete($image->file);
                    $image->forceDelete();
                }

                // Now, create entries for the new gallery files
                foreach ($galleryFiles as $upload) {
                    if ($upload instanceof UploadedFile) {
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                            'file' => $this->handleFileUpload($upload, 'properties'),
                            'type' => $this->detectFileType($upload),
                            'is_primary' => PropertyImage::NOT_PRIMARY,
                        ]);
                    }
                }
            }
        });
    }

    private function detectFileType(UploadedFile $file): int
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return PropertyImage::TYPE_IMAGE;
        } elseif (str_starts_with($mime, 'video/')) {
            return PropertyImage::TYPE_VIDEO;
        }

        return PropertyImage::TYPE_UNKNOWN;
    }



    public function delete(Property $property): void
    {
        $property->update(['deleted_by' => Auth::id(), 'status' => Property::STATUS_DELETED]);
        $property->delete();
    }
    public function apiDelete(Property $property): void
    {
        $property->update(['deleted_by' => Auth::id(), 'status' => Property::STATUS_DELETED]);
    }
    public function apiArchive(Property $property): void
    {
        $property->update(['updated_by' => Auth::id(), 'status' => Property::STATUS_ARCHIVE]);
    }
    public function apiRenew(Property $property): void
    {
        $property->update(['renew_at' => now(), 'status' => Property::STATUS_OPEN]);
    }
    public function apiPermanentDelete(Property $property): void
    {
        $property->update(['deleted_by' => Auth::id(), 'status' => Property::STATUS_DELETED]);
        $property->delete();
    }

    public function restore(string $encryptedId): void
    {
        $property = $this->getDeletedProperty($encryptedId);
        $property->update(['updated_by' => Auth::id()]);
        $property->restore();
    }
    public function changePropertyStatus(Property $property, string $newStatus): void
    {
        $property->update([
            'status' => $newStatus,
            'renew_at' => now(),
            'updated_by' => Auth::id()
        ]);
    }

    public function permanentDelete(string $encryptedId): void
    {
        $property = $this->getDeletedProperty($encryptedId);
        $images = $property->images()->get();
        foreach ($images as $image) {
            $this->fileDelete($image->file);
        }
        $property->forceDelete();
    }

    public function toggleStatus(Property $property): void
    {
        $property->update([
            'status' => !$property->status,
            'updated_by' => Auth::id()
        ]);
    }
}
