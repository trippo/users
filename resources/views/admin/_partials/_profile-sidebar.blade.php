<div class="box box-primary">
    <div class="box-body box-profile">
        <img class="profile-user-img img-responsive img-circle"
             src="{{ get_image($object->avatar) }}"
             alt="{{ $object->display_name or '' }}">
        <h3 class="profile-username text-center">{{ $object->display_name or '' }}</h3>
        @php
            $status = isset($object->status) ? $object->status : null;
            try {
                $deleted = $object->trashed();
            } catch(Exception $exception) {
                $deleted = false;
            }
        @endphp
        <p class="text-center">
            {!! $status !== null ? html()->label($status, $status) : '' !!}
            {!! $deleted ? html()->label('deleted', 'deleted') : '' !!}
        </p>
    </div>
    <div class="box-body">
        <b class="control-label">Username</b>
        <div class="text-muted mb20">
            {{ $object->username or '' }}
        </div>
        <b class="control-label">Email</b>
        <div class="text-muted mb20">
            {{ $object->email or '' }}
        </div>
        <b class="control-label">Sex</b>
        <div class="text-muted mb20">
            {{ $object->sex or '...' }}
        </div>
        <b class="control-label">Phone</b>
        <div class="text-muted mb20">
            {{ $object->phone or '' }}
        </div>
        <b class="control-label">Mobile phone</b>
        <div class="text-muted mb20">
            {{ $object->mobile_phone or '' }}
        </div>
        <b class="control-label">About me</b>
        <div class="text-muted">
            {{ $object->description or '...' }}
        </div>
    </div>
</div>
