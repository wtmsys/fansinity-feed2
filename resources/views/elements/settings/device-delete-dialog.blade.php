<div class="modal fade" tabindex="-1" role="dialog" id="device-delete-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Delete device')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Are you sure you want to delete this device?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="PrivacySettings.removeDevice();">{{__('Delete')}}</button>
            </div>
        </div>
    </div>
</div>
