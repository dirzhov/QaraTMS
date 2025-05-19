<script language="javascript">
    var reviewModalEl = document.getElementById('test_case_overlay')
    reviewModalEl.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var test_result_id = button.getAttribute('data-bs-test_result_id')

        // $('#test_case_with_result_overlay_data').load(`/test-case-with-result-overlay/${test_result_id}`, function () {
        $('#test_case_overlay_data').load(`/test-case-with-result-overlay/${test_result_id}`, function () {
        });
    })
</script>