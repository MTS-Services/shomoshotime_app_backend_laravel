import select2 from "select2";
import "select2/dist/css/select2.min.css";

window.Select2 = select2;
$(document).ready(function () {
    $('.select2').each(function () {
        new Select2(this);
    })

    $('select.select2').each(function () {
        $(this).select2({
            tags: true,
            tokenSeparators: [','],
        });
    })

});