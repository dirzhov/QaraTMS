<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Test Result Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="review_id" id="review_id" value="">
                    <input type="hidden" name="test_result_id" id="test_result_id" value="">

                    <div class="mb-3">
                        <label for="reviewer" class="col-form-label">Reviewer:</label>
                        <select id="reviewer_select" name="reviewer" class="selectpicker ms-2">
                            <option value="0">Unassigned</option>
                            @foreach($projectActiveUsers as $reviewer)
                                <option value="{{$reviewer->id}}">{{$reviewer->name}}</option>
                            @endforeach
                        </select>

                        <button type="button" class="btn btn-link ms-2" id="assignToMe">Assign to me</button>
                    </div>
                    <div class="mb-3">
                        <label for="review_status" class="col-form-label">Status:</label>
                        <select id="review_status_select" name="review_status" class="selectpicker">
                            @foreach (App\Enums\TestResultReviewStatus::cases() as $option)
                                <option value="{{$option->value}}" data-content="<i class='bi {{$option->cls()}} me-1'></i
                                >{{ucwords(str_replace('_', ' ',mb_strtolower($option->name)))}}"></option>
                            @endforeach
                        </select>
                        <div class="form-check form-check-inline ms-4">
                        <input class="form-check-input" type="checkbox" value="" id="isFixedCheck">
                        <label class="form-check-label" for="isFixedCheck">Is fixed?</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="select_issue" class="col-form-label">Issues:</label>
                        <div class="col-9 ms-5 d-inline-block">
                            <input type="text" class="form-control" placeholder="Add Issue" name="select_issue" id="select_issue"
                                   data-filter="{{ Config::get('app.jira_host') }}/rest/api/2/issue/#QUERY#?fields=summary,priority,status,issuetype">
                        </div>
                    </div>
                    <div class="form-group m-1 position-relative">
                        <ul class="list-group list-group-flush" id="issues_list">
                        </ul>
                        <input type="hidden" name="issues" id="issues_input" value="">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label">Comment:</label>
                        <textarea rows="4" class="form-control" id="review_comment"></textarea>
                    </div>
                    <div>
                        <small>Last update: <span id="review_updated" class="text-secondary"></span></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeBtn">Close</button>
                <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        const lastUpdated = '#review_updated'
        const reviewer = '#reviewer_select'
        const assignToMe = '#assignToMe'
        const status = '#review_status_select'
        const isFixed = '#isFixedCheck'
        const addIssue = '#review_issues'
        const comment = '#review_comment'
        const testResultId = '#test_result_id'
        const saveBtn = '#saveBtn'

        const selectIssue = '#select_issue'
        const issuesInput = '#issues_input'
        const issuesList = '#issues_list'

        function disableAll(isDisabled) {
            $(reviewer).prop('disabled', isDisabled);
            $(reviewer).selectpicker('refresh');
            $(assignToMe).prop('disabled', isDisabled);
            $(status).prop('disabled', isDisabled);
            $(status).selectpicker('refresh');
            $(isFixed).prop( "disabled", isDisabled);
            $(selectIssue).prop('disabled', isDisabled);
            $(addIssue).prop('disabled', isDisabled);
            $(comment).prop('disabled', isDisabled);
            $(saveBtn).prop('disabled', isDisabled);
        }
        function resetControls() {
            test_result_id = null
            review_id = null
            $(reviewer).selectpicker('val', "0");
            $(reviewer).selectpicker('refresh');
            $(status).selectpicker('val', '{{\App\Enums\TestResultReviewStatus::NOT_REVIEWED}}');
            $(status).selectpicker('refresh');
            $(isFixed).prop( "checked", false );
            $(selectIssue).val("");
            $(issuesList).issuelist("clear");
            $(issuesList).issuelist("refresh");
            $(comment).val("");
            $(testResultId).val("");
        }

        var test_result_id = null
        var review_id = null

        var reviewModalEl = document.getElementById('reviewModal')
        reviewModalEl.addEventListener('show.bs.modal', function (event) {
            resetControls();
            disableAll(true);
            var button = event.relatedTarget
            test_result_id = button.getAttribute('data-bs-test_result_id')
            $(testResultId).val(test_result_id);

            var jqxhr = $.get( `/test_result_review/${test_result_id}`, (resp) => {
                disableAll(false);
                if (resp.data != null) {
                    review_id = resp.data.id;
                    $(lastUpdated).text(dayjs(resp.data.created_at).format('YYYY/DD/MM HH:mm:ss'));
                    $(reviewer).selectpicker('val', resp.data.reviewer_id == null ? 0 : resp.data.reviewer_id.toString());
                    if(resp.data.reviewer_id == userId || resp.data.status == 4) {
                        $(reviewer).prop('disabled', true);
                        $(assignToMe).prop('disabled', true);
                    } else {
                        $(reviewer).prop('disabled', false);
                        $(assignToMe).prop('disabled', false);
                    }
                    $(reviewer).selectpicker('refresh');

                    $(status).selectpicker('val', resp.data.status.toString());
                    $(status).selectpicker('refresh');
                    $(isFixed).prop( "checked", resp.data.is_fixed==1);
                    $(comment).val(resp.data.comment);
                    $(issuesInput).val(resp.data.issues)

                    $(issuesList).issuelist("_fetchPresetData", resp.data.issues);
                    $(issuesList).issuelist("refresh");

                    var selectedUser = parseInt($(reviewer).val());
                    if (selectedUser > 0 && selectedUser != userId) {
                        disableAll(true)
                    }
                }
            })
            // .done(function() {
            // })
            // .fail(function(r) {
            // })
            .always(function(r) {

                if (r.status == 403)
                    disableAll(true);
            });

        })

        reviewModalEl.addEventListener('hide.bs.modal', function (event) {
            resetControls();
        })

        $(saveBtn).on('click', () => {

            $.ajax({
                url: `/test_result_review`, // + (review_id==null) ? "create" : "update"
                method: (review_id==null) ? "POST" : "PUT",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": review_id,
                    "review_test_result_id": test_result_id,
                    "reviewer_id": $(reviewer).val(),
                    "review_status": $(status).val(),
                    "is_fixed": $(isFixed).is(":checked"),
                    "issues": $('#issues_input').val(),
                    "review_comment": $(comment).val()
                }
            })
            .done( (resp) => {
                // $row = $('#table').bootstrapTable('getRowByUniqueId', resp.data.rtr_id)
                $('#table').bootstrapTable('updateCellByUniqueId', {
                    id: resp.data.rtr_id,
                    field: 'issues',
                    value: resp.data.issues,
                    reinit: false
                })
                $('#table').bootstrapTable('updateCellByUniqueId', {
                    id: resp.data.rtr_id,
                    field: 'review_status',
                    value: resp.data.status,
                    reinit: false
                })
                $('#table').bootstrapTable('updateCellByUniqueId', {
                    id: resp.data.rtr_id,
                    field: 'reviewer_id',
                    value: resp.data.reviewer_id,
                    reinit: false
                })

                $('#reviewModal').modal('hide')
                resetControls()
            })

        });

        $(assignToMe).on('click', () => {
            if ($(reviewer).val() == 0) {
                $(status).selectpicker('val', {{\App\Enums\TestResultReviewStatus::NOT_REVIEWED}});
                $(status).selectpicker('refresh');
            }
            $(reviewer).selectpicker('val', userId.toString())
        });

        createIssueList('#select_issue', issuesInput, issuesList)

    })
</script>
