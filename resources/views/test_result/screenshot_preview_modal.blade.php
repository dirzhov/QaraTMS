<div id="screenshotModal" class="modal fade" tabindex="-1" aria-labelledby="screenshotModalFullscreenLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: fixed;right: 10px;"></button>
                <img src="" id="screenshot">
        </div>
    </div>
</div>
<script>
    $(function() {
        let modalEl = document.getElementById('screenshotModal')
        modalEl.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget
            let url = button.getAttribute('data-bs-url')
            $('#screenshot').attr('src', url);
        });
    })
</script>