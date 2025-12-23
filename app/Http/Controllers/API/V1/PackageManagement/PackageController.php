<?php

namespace App\Http\Controllers\API\V1\PackageManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PackageManagement\PaymentRequest;
use App\Http\Resources\API\V1\PackageManagement\PackageResource;
use App\Services\PackageManagement\KnetPaymentService;
use App\Services\PackageManagement\OrderService;
use App\Services\PackageManagement\PackageService;
use App\Services\PackageManagement\PaymentService;
use App\Services\PackageManagement\UserAdService;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    protected PackageService $packageService;

    protected KnetPaymentService $knetPaymentService;
    protected OrderService $orderService;
    protected UserAdService $userAdService;

    protected PaymentService $paymentService;

    public function __construct(PackageService $packageService, KnetPaymentService $knetPaymentService, OrderService $orderService, UserAdService $userAdService, PaymentService $paymentService)
    {
        $this->packageService = $packageService;
        $this->knetPaymentService = $knetPaymentService;
        $this->orderService = $orderService;
        $this->userAdService = $userAdService;
        $this->paymentService = $paymentService;
    }


    public function all()
    {
        $packages = $this->packageService->getPackages()->active()->latest()->get();
        return sendResponse(true, 'All packages retrieved successfully.', PackageResource::collection($packages), Response::HTTP_OK);
    }


    public function buyPackage($packageId)
    {
        try {
            return DB::transaction(function () use ($packageId) {
                $package = $this->packageService->getPackage(encrypt($packageId));
                if (!$package) {
                    return sendResponse(false, 'Package not found.', null, Response::HTTP_NOT_FOUND);
                }


                $data['package_id'] = $package->id;
                $data['amount'] = $package->price;
                $data['total_ad'] = $package->total_ad;
                $order = $this->orderService->createOrder($data);

                $adData['package_id'] = $package->id;
                $adData['order_id'] = $order->id;
                $adData['amount'] = $order->amount;
                $adData['total_ad'] = $order->total_ad;
                $adData['ad_type'] = $package->tag;

                $this->userAdService->createUserAd($adData);

                $paymentData['order_id'] = $order->id;
                $paymentData['amount'] = $order->amount;
                $paymentData['transaction_id'] = generateTransactionID();
                $paymentData['customer_email'] = Auth::user()->email ?? null;
                $paymentData['customer_phone'] = Auth::user()->phone ?? null;
                $paymentData['description'] = 'New Order -' . $order->order_id;
                $paymentData['user_id'] = Auth::id();
                $paymentData['currency'] = config('knet.currency');
                $paymentData['language'] = config('knet.language');
                $paymentData['processed_at'] = now();
                $paymentData['request_data'] = json_encode(array_merge($data, $adData));


                $payment = $this->paymentService->createPayment($paymentData);
                $result = $this->knetPaymentService->initiatePayment($paymentData, $payment->transaction_id);


                if ($result['success']) {
                    return sendResponse(true, 'Order initiated successfully. Please proceed with the payment to finalize your order.', ['payment_url' => $result['payment_url']], Response::HTTP_OK);
                }

                return sendResponse(false, 'Failed to initiate payment.', null, Response::HTTP_INTERNAL_SERVER_ERROR);




            });
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage() . ' ' . $error->getLine() . ' ' . $error->getFile(), null, Response::HTTP_NOT_FOUND);
        }


    }

    public function handlePaymentResponse(Request $request): JsonResponse
    {
        $result = $this->knetPaymentService->handlePaymentResponse($request->all());

        if ($result['success']) {
            return sendResponse(true, 'Payment completed successfully.', $result, Response::HTTP_OK);
        }

        return sendResponse(false, 'Failed to complete payment.', $result, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
