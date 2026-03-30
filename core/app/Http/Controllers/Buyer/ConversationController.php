<?php

namespace App\Http\Controllers\Buyer;

use App\Events\LiveChat;
use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Conversation;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\PostedJob;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function index()
    {
        $pageTitle     = "Conversations";
        $buyer         = auth()->guard('buyer')->user();
        $conversation  = null;
        $freelancer    = null;
        $messages      = null;
        $conversations = Conversation::where('buyer_id', $buyer->id)
            ->with(['user', 'messages', 'buyer'])
            ->orderByDesc('updated_at')
            ->get();
        return view('Template::buyer.conversation.index', compact('pageTitle', 'buyer', 'freelancer', 'messages', 'conversations'));
    }

    public function jobPortalChat(PostedJob $postedJob, User $user)
    {
        $buyer = auth()->guard('buyer')->user();
        abort_unless((int) $postedJob->buyer_id === (int) $buyer->id, 403);

        $selected = JobApplication::query()
            ->where('job_id', $postedJob->id)
            ->where('user_id', $user->id)
            ->where('status', JobApplication::STATUS_SELECTED)
            ->exists();
        abort_unless($selected, 403);

        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => $buyer->id,
                'user_id'  => $user->id,
            ],
            []
        );

        return $this->conversation($conversation->id);
    }

    public function bidChat($id)
    {
        $pageTitle = "Conversation";
        $buyer     = auth()->guard('buyer')->user();

        $bid = Bid::where('id', $id)->firstOrFail();
        $job = $bid->job;

        $freelancer   = $bid->user;
        $conversation = Conversation::where('buyer_id', $buyer->id)->with(['user', 'buyer', 'messages'])
            ->where('user_id', $freelancer->id)
            ->first();

        if (!$conversation) {
            $conversation           = new Conversation();
            $conversation->buyer_id = $buyer->id;
            $conversation->user_id  = $freelancer->id;
            $conversation->save();

            $job->interviews += 1;
            $job->save();
        }

        $messages = $conversation->messages;
        $buyer    = auth()->guard('buyer')->user();

        $conversations = Conversation::where('buyer_id', $buyer->id)
            ->with(['user', 'messages', 'buyer'])
            ->orderByDesc('updated_at')
            ->get();

        $id = $conversation->id;
        return view('Template::buyer.conversation.index', compact('pageTitle', 'buyer', 'freelancer', 'messages', 'conversation', 'conversations', 'id'));
    }

    public function conversation($id)
    {

        $pageTitle    = "Conversation";
        $buyer        = auth()->guard('buyer')->user();
        $conversation = Conversation::where('id', $id)
            ->where('buyer_id', $buyer->id)
            ->with(['user', 'buyer', 'messages'])
            ->firstOrFail();
        $freelancer   = @$conversation->user;

        // Mark unread messages as read
        Message::where('conversation_id', $conversation->id)->whereNull('buyer_read_at')->where(function ($query) use ($freelancer) {
            $query->where('user_id', $freelancer->id);
        })->update(['buyer_read_at' => now()]);

        $messages = $conversation->messages;

        $buyer         = auth()->guard('buyer')->user();
        $conversations = Conversation::where('buyer_id', $buyer->id)
            ->with(['user', 'buyer', 'messages'])
            ->orderByDesc('updated_at')
            ->get();

        Message::whereHas('conversation', function ($query) use ($buyer) {
            $query->where('buyer_id', $buyer->id);
        })
            ->whereNull('buyer_read_at')
            ->where('user_id', '>', 0)
            ->update(['buyer_read_at' => now()]);

        $id = $conversation->id;
        return view('Template::buyer.conversation.index', compact('pageTitle', 'buyer', 'freelancer', 'messages', 'conversation', 'conversations', 'id'));
    }

    public function conversationStore(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'message'         => 'required',
            'message_files'   => ['nullable', 'array', 'max:10'],
            'message_files.*' => ['nullable', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG', 'pdf', 'PDF', 'docx', 'DOCX', 'doc', 'DOC'])],
        ]);

        if ($validation->fails()) {
            return responseError('validation_error', $validation->errors()->all());
        }

        $conversation = Conversation::unblock()
            ->where('id', $id)
            ->where('buyer_id', auth()->guard('buyer')->id())
            ->firstOrFail();

        if (!($request->message_files) && !($request->message)) {
            $notify[] = 'Message field is required';
            return responseError('validation_required', $notify);
        }

        $broadcast = initializePusher();

        if ($request->message_files) {
            foreach ($request->message_files as $messageFile) {
                try {
                    $message_files[] = fileUploader($messageFile, getFilePath('message'));
                } catch (\Exception $exp) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Couldn\'t upload your files',
                    ]);
                }
            }
        }

        $message                  = new Message();
        $message->message         = $request->message;
        $message->files           = @$message_files;
        $message->conversation_id = $id;
        $message->buyer_id        = $conversation->buyer_id;
        $message->buyer_read_at   = now();
        $message->save();

        $conversation->touch();

        if ($broadcast) {
            event(new LiveChat($message));
        }

        $notify[] = 'Successfully sent message';
        return responseSuccess('sent_message', $notify);
    }

    public function download($filename)
    {
        $filePath = public_path(getFilePath('message') . $filename);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        abort(404, 'File not found.');
    }

    public function blockStatus($id)
    {
        return Conversation::changeStatus($id);
    }
}
