<div id="issues_list" class="p-2">
Loading..
</div>
<script>

    function loadDefects(url, locator) {
        $.ajax({
            url: url,
            type: 'get',
            headers: {
                'Authorization': 'Bearer ' + '{{ Config::get('app.api_token') }}',
                'Content-Type': 'application/json'
            },
            success: function (data) {
                $(locator).html("");
                var html = "";
                for (const issue of data.data) {
                    html += `<div class="my-2 border-bottom">`;
                    html += `
<span>
<span class="d-inline-block" style="width:80px">
    <img src="${issue.status.iconUrl}" title="${issue.status.name}" class="me-1">
    <span>${issue.status.name}</span>
</span
<span class="me-1">
    <img src="${issue.priority.iconUrl}" title="${issue.priority.name}" width="16">
</span>
<a href="${issue.i_url}" target="_blank" class="me-2 ${issue.status.name=='Closed'?'text-decoration-line-through':''}">${issue.i_key}</a>
<span class="${issue.status.name=='Closed'?'text-decoration-line-through':''}">${issue.i_summary}</span>
</span>
                    `;
                    if (issue.tcs) {
                        html += '<span class="d-inline-block float-end">';
                        for (const tc of issue.tcs) {
                            var priority = renderPriority(tc.priority);
                            html += `<span class="ms-1">
${priority}
<a href="/test-case/${tc.id}" target="_blank">${tc.prefix}-${tc.id}</a></span>`
                        }
                        html += '<span/>';
                    }
                    html += `</div>`;
                }
                $(locator).append(html);

            },
            error: function (err) {
                errorToast(err.responseJSON.message);
            }
        });
    }

    var tabEl = document.querySelector('button[id="nav-all-app-defects-tab"]')

    tabEl.addEventListener('shown.bs.tab', function (event) {
        loadDefects("/api/statistics/get_all_app_defects/{{$testRun->id}}", '#issues_list');
    })
</script>