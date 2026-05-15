@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="dashboard-section pt-80">
        <div class="container">
            <div class="pb-80">
                <div class="message__chatbox bg--section">
                    <div class="message__chatbox__header">
                        <h6 class="title">
                            @php echo $myTicket->statusBadge; @endphp
                            @lang('Ticket Id') : <span class="text--base">[#{{ $myTicket->ticket }}]
                                {{ $myTicket->subject }}</span>
                            </h6>
                    </div>
                    <div class="message__chatbox__body">
                        @if ($myTicket->TICKET_CLOSE != 4)
                            <form class="message__chatbox__form row" method="post"
                                action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="replayTicket" value="1">
                                <div class="form--group col-sm-12">
                                    <textarea class="form-control form--control" name="message" placeholder="@lang('Enter Message')" required=""></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="cmn--btn btn-sm addFile"><i class="fa fa-plus"></i> @lang("Ajouter nouveau")</button>
                                </div>
                                <div class="form-group">
                                    <label for="inputAttachments">@lang('Pièces jointes')</label>
                                    <small class="text--danger">@lang('Max 5 files can be uploaded'). @lang('Maximum upload size is') {{ ini_get('upload_max_filesize') }}</small>
                                    <div class="position-relative">
                                        <input class="form-control form--control custom--file-upload my-1" id="inputAttachments" name="attachments[]" type="file" />
                                    </div>
                                    <div id="fileUploadsContainer"></div>
                                </div>
                                <div class="form--group col-sm-12 mt-2 mb-0">
                                    <button type="submit" class="cmn--btn btn--lg">@lang('Send Message')</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="pb-80">
                <div class="message__chatbox bg--section">
                    <div class="message__chatbox__body">
                        <ul class="reply-message-area">
                            @foreach ($messages as $message)
                                <li>
                                    @if ($message->admin_id == 0)
                                    <div class="reply-item ms-auto">
                                        <div class="name-area">
                                            <h6 class="title">{{ __($message->ticket->name) }}</h6>
                                        </div>
                                        <div class="content-area">
                                            <span class="meta-date">
                                                @lang('Posted on') <span
                                                    class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i') }}</span>
                                            </span>
                                            <p>
                                                {{ __($message->message) }}
                                            </p>
                                            @if ($message->attachments()->count() > 0)
                                                <div class="mt-2">
                                                    @foreach ($message->attachments as $k => $image)
                                                        <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                            class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                            {{ ++$k }} </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                        <div class="reply-item">
                                            <div class="name-area">
                                                <h6 class="title">{{ __($message->admin->name) }}</h6>
                                            </div>
                                            <div class="content-area">
                                                <span class="meta-date">
                                                    @lang('Posted on'), <span
                                                        class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i') }}</span>
                                                </span>
                                                <p>
                                                    {{ __($message->message) }}
                                                </p>
                                                @if ($message->attachments()->count() > 0)
                                                    <div class="mt-2">
                                                        @foreach ($message->attachments as $k => $image)
                                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                                class="me-3"><i class="fa fa-file"></i>
                                                                @lang('Attachment') {{ ++$k }} </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFile').on('click', function() {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                    <div class="form-group d-flex gap-3 my-2">
                        <div class="position-relative w-100">
                            <input type="file" id="inputAttachments" name="attachments[]" class="form-control form-control form--control custom--file-upload" required/>
                        </div>
                        <button class="btn--danger btn  remove-btn" type="button"><i class="las la-times"></i></button>
                    </div>
                `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.form-group').remove();
            });
        })(jQuery);
    </script>
@endpush
@push('style')
<style>
    .reply-item{
        width: 90%;
    }
</style>
@endpush
