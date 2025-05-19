<div id="testlogModal" class="modal fade" tabindex="-1" aria-labelledby="testlogModalFullscreenLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="position: fixed;right: 10px; background-color: white;"></button>
            <div id="testlog" class="console-log"></div>
        </div>
    </div>
</div>
<script>
    $(function() {
        let modalEl = document.getElementById('testlogModal')
        modalEl.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget
            let url = button.getAttribute('data-bs-url')
            $.get(url, resp => {
                const ansi_up = new AnsiUp();
                $('#testlog').html(ansi_up.ansi_to_html(resp).replaceAll('\n',"<br/>"));
            })

        });
    })
</script>