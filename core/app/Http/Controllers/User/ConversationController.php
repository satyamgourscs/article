<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Events\LiveChat;
use App\Models\Conversation;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\PostedJob;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function fromPostedJob(PostedJob $postedJob)
    {
        $user = auth()->user();

        $selected = JobApplication::query()
            ->where('job_id', $postedJob->id)
            ->where('user_id', $user->id)
            ->where('status', JobApplication::STATUS_SELECTED)
            ->exists();
        abort_unless($selected, 403);

        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => $postedJob->buyer_id,
                'user_id'  => $user->id,
            ],
            []
        );

        return redirect()->route('user.conversation.index', $conversation->id);
    }

    public function index($id = 0)
    {
        $pageTitle = "Conversations";
        $freelancer = auth()->user();
        $conversation = NULL;
        $messages = NULL;
        $buyer = NULL;
        $id = $id;

        if ($id) {
            $conversation = Conversation::unblock()
                ->where('id', $id)
                ->where('user_id', $freelancer->id)
                ->with(['job', 'user', 'buyer', 'messages'])
                ->first();
            if (!$conversation) {
                $notify[] = ['error', 'You are temporary blocked this conversation'];
                return back()->withNotify($notify);
            }

            $buyer = $conversation->buyer;
            $id = $conversation->id;

            Message::where('conversation_id', $conversation->id)
                ->whereNull('read_at')
                ->where(function ($query) use ($freelancer) {
                    $query->where('user_id', 0)->orWhere('user_id', '!=', $freelancer->id);
                })
                ->update(['read_at' => now()]);

            $messages = $conversation->messages;
        }

        $conversations = Conversation::where('user_id', $freelancer->id)
            ->with(['user', 'buyer', 'messages'])
            ->orderByDesc('updated_at')
            ->get();

            Message::whereHas('conversation', function ($query) use ($freelancer) {
                $query->where('user_id', $freelancer->id);
            })
                ->whereNull('read_at')
                ->where(function ($query) use ($freelancer) {
                    $query->where('user_id', 0)->orWhere('user_id', '!=', $freelancer->id);
                })
                ->update(['read_at' => now()]);

        return view('Template::user.conversation.index', compact('pageTitle', 'buyer', 'freelancer', 'messages', 'conversation', 'conversations', 'id'));
    }

    public function conversationStore(Request $request, $id)
    {
        $validation  = Validator::make($request->all(), [
            'message'                => 'required',
            'message_files'          => ['nullable', 'array', 'max:10'],
            'message_files.*'        => ['nullable', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG', 'pdf', 'PDF', 'docx', 'DOCX', 'doc', 'DOC'])],
        ]);


        if ($validation->fails()) {
            return responseError('validation_error', $validation->errors()->all());
        }

        $conversation = Conversation::unblock()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!($request->message_files) && !($request->message)) {
            $notify[] = 'Message field is required';
            return responseError('validation_required', $notify);
        }

        $broadcast = initializePusher();

        if ($request->message_files) {
            foreach ($request->message_files as $message_file) {
                try {
                    $message_files[] = fileUploader($message_file, getFilePath('message'));
                } catch (\Exception $exp) {
                    $notify[] = 'Couldn\'t upload your files: ' . $exp;
                    return responseError('upload_failed', $notify);
                }
            }
        }

        $message                  = new Message();
        $message->message         = $request->message;
        $message->files           = @$message_files;
        $message->conversation_id = $id;
        $message->user_id         = auth()->id();
        $message->read_at         = now();
        $message->save();

        $conversation->touch();

        if ($broadcast) {
            event(new LiveChat($message));
        }


        $notify[] = 'Successfully sent message';
        return  responseSuccess('sent_message', $notify);
    }
}
