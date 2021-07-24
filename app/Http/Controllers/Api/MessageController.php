<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Resources\ChatMessageResource;
use App\Message;
use App\User;
use App\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class MessageController extends Controller
{

    /**
     *  User Block   conversation
     * @user_block_conversation
    */
    public function user_block_conversation(Request $request , $id)
    {
        $lang = $request->server('HTTP_ACCEPT_LANGUAGE');
        $conversation = Conversation::find($id);
        if ($conversation)
        {
            if ($conversation->blocked == '1')
            {
                $errors = [
                    'key' => 'user_block_conversation',
                    'value' => $lang == 'en' ? 'this Conversation are added to black list before' : 'المحادثة  في  القائمه السوداء  بالفعل',
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
            // check  if  user  in conversation or  not
            if ($request->user()->id == $conversation->first || $request->user()->id == $conversation->second)
            {
                // block conversation
                $rules = [
                    'block_reason'  => 'nullable',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                $conversation->update([
                    'blocked' => '1',
                    'block_maker' => $request->user()->id,
                    'block_reason' => $request->block_reason == null ? null : $request->block_reason,
                ]);
                $success = [
                    'key' => 'user_block_conversation',
                    'value' =>  $lang == 'ar' ? 'تم أضافه  المحادثة  الي القائمه  السوداء' :'conversation are added to  black list  successfully',
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key' => 'user_block_conversation',
                    'value' => $lang == 'en' ? 'You Are Not In this  Conversation' : 'انت لست  عضو في هذة المحادثة',
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $errors = [
                'key' => 'user_block_conversation',
                'value' => $lang == 'en' ? 'Conversation Not Found' : 'المحادثة  غير  موجوده',
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function send_message(Request $request){
        $request['first'] = $request->user()->id;
        $room = Conversation::whereId($request->room_id)
            ->where('first_online','1')
            ->where('second_online','1')
            ->first();
        if ($room){
            $request['seen']='1';
            $room->update(['seen'=>'1']);
        }else{
            $room = Conversation::find($request->room_id);
            if ($room->first_online == '0'){
                /* notifications*/
                $devicesTokens = UserDevice::where('user_id', $room->first)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                $ar_message = $request->file == null ? $request->message : 'تم  ارسال مرفق';
                if ($devicesTokens) {
                    sendMultiNotification('الرسايل', $request->user()->name." : ". $ar_message ,$devicesTokens);
                }
                //end notifications/
                // $room->update(['sender_online'=>1]);
            }else{
                /* notifications*/
                $devicesTokens = UserDevice::where('user_id', $room->second)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                $ar_message = $request->file == null ? $request->message : 'تم  ارسال مرفق';
                if ($devicesTokens) {
                    sendMultiNotification('الرسايل', $request->user()->name." : ". $ar_message ,$devicesTokens);
                }
            }
            $room->update(['seen'=>'0']);
        }
        if ($request->file != null)
        {
            $chat =  Message::create([
                'conversation_id' => $request->room_id,
                'user_id'   => $request->user()->id,
                'file' => $request->file,
            ]);
        }else{
            $chat =  Message::create([
                'conversation_id' => $request->room_id,
                'user_id'   => $request->user()->id,
                'message' => $request->message,
            ]);
        }
        $data=  new \App\Http\Resources\Message($chat);
        return response()->json(['mainCode'=> 1,'code' =>  200 , 'data'=>$data],200);
    }
    public function connect_room(Request $request){
        $rules = [
            'room_id'  => 'required|exists:conversations,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $room= Conversation::find($request->room_id);
        if ($room->first == $request->user()->id){
            $room->update(['first_online'=>'1']);
        }else{
            $room->update(['second_online'=>'1']);
        }
        return response()->json(['mainCode'=> 1,'code' =>  200 , 'user_phone'=>$request->user()->phone_number ],200);

    }
    public function disconnect_room(Request $request){

//        $rules = [
//            'phone'  => 'required|exists:users,phone_number',
//            'room_id'  => 'required|exists:conversations,id'
//        ];
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails())
//            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $room= Conversation::find($request->room_id);
        $user = User::where('phone_number',$request->phone)->first();
        if($user){
            if ($room->first == $user->id){
                $room->update(['first_online'=>'0']);
            }else{
                $room->update(['second_online'=>'0']);
            }
            return response()->json(['mainCode'=> 1,'code' =>  200 , 'phone'=>$user->phone_number ]);

        }else{
            return response()->json(['mainCode'=> 0,'code' =>  422 ,'message'=> 'user_disconnect' ]);
        }
    }
    public function create_conversation(Request $request , $id)
    {
        $lang = $request->server('HTTP_ACCEPT_LANGUAGE');
        $receiver = User::find($id);
        if ($receiver == null)
        {
            $errors = [
                'key'   => 'create_conversation',
                'message' => $lang == 'ar' ? 'لا يوجد هذا المستخدم' : 'User Not Found'
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
        $check_conversation = Conversation::whereFirst($request->user()->id)
            ->where('second' , $receiver->id)
            ->orWhere('second' , $request->user()->id)
            ->where('first' , $receiver->id)
            ->first();
        if ($check_conversation == null)
        {
            $conversation = Conversation::create([
                'first'  => $request->user()->id,
                'second' => $receiver->id
            ]);
            $arr = [];
            array_push($arr, [
                'id'    => $conversation->id,
                'first' => intval($conversation->first),
                'second' => intval($conversation->second),
                'first_online' => intval($conversation->first_online),
                'second_online' => intval($conversation->second_online),
                'seen' => intval($conversation->seen),
                'created_at' => $conversation->created_at->format('y-m-d'),
            ]);
            return ApiController::respondWithSuccess($arr);
        }else{

            $con = $check_conversation;
            $arr = [];
            array_push($arr, [
                'id'    => $con->id,
                'first' => intval($con->first),
                'second' => intval($con->second),
                'first_online' => intval($con->first_online),
                'second_online' => intval($con->second_online),
                'seen' => intval($con->seen),
                'created_at' => $con->created_at->format('y-m-d'),
            ]);
            return ApiController::respondWithSuccess($arr);
        }
    }

//    public function send_message(Request $request)
//    {
//        $lang = $request->server('HTTP_ACCEPT_LANGUAGE');
//        $rules = [
//            'user_id'  => 'required|exists:users,id',
//            'message'  => 'required'
//        ];
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails())
//            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
//
//        $receiver = User::find($request->user_id);
//        $check_conversation = Conversation::whereFirst($request->user()->id)
//            ->where('second' , $receiver->id)
//            ->orWhere('second' , $request->user()->id)
//            ->where('first' , $receiver->id)
//            ->first();
//        if ($check_conversation == null)
//        {
//            $conversation = Conversation::create([
//                'first'  => $request->user()->id,
//                'second' => $receiver->id
//            ]);
//            $messages = Message::whereConversationId($conversation->id)
//                ->orderBy('id' , 'desc')
//                ->get();
//            // create a new message
//            $message = Message::create([
//                'conversation_id'   => $conversation->id,
//                'message'           => $request->message,
//                'user_id'           => $request->user()->id,
//            ]);
//            $arr = [];
//            array_push($arr , [
//                'id'              => $message->id,
//                'conversation_id' => $message->conversation->id,
//                'user_id'         => $message->user_id,
//                'user'            => $message->user->name,
//                'message'         => $message->message,
//                'created_at'      => $message->created_at->format('Y-m-d'),
//             ]);
//
//            return ApiController::respondWithSuccess($arr);
//        }else{
//            // create a new message
//            $message = Message::create([
//                'conversation_id'   => $check_conversation->id,
//                'message'           => $request->message,
//                'user_id'           => $request->user()->id,
//            ]);
//            $arr = [];
//            array_push($arr , [
//                'id'              => $message->id,
//                'conversation_id' => $message->conversation->id,
//                'user_id'         => $message->user_id,
//                'user'            => $message->user->name,
//                'message'         => $message->message,
//                'created_at'      => $message->created_at->format('Y-m-d'),
//            ]);
//
//            return ApiController::respondWithSuccess($arr);
//        }
//    }
    public function my_conversations(Request $request)
    {
        $conversations = Conversation::whereFirst($request->user()->id)
            ->where('block' , '0')
            ->where('status' , '0')
            ->orWhere('second' , $request->user()->id)
            ->where('block' , '0')
            ->where('status' , '0')
            ->orderBy('id' , 'desc')
            ->get();
        if ($conversations->count() > 0)
        {
            $arr = [];
            foreach ($conversations as $conversation)
            {
                $last = Message::whereConversationId($conversation->id)->orderBy('id' , 'desc')->first();
                array_push($arr , [
                    'room_id'      => $conversation->id,
                    'seen' => intval($conversation->seen),
                    'user_id' => $conversation->first == $request->user()->id ? $conversation->second : $conversation->first,
                    'name'    => $conversation->first == $request->user()->id ? $conversation->the_second->name : $conversation->the_first->name,
                    'photo'   => $conversation->first == $request->user()->id ? ($conversation->the_second->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$conversation->the_second->photo)) : ($conversation->the_first->photo == null ? asset('/uploads/users/default.png') :asset('/uploads/users/'.$conversation->the_first->photo)),
                    'last_message' => $last == null ? null : $last->message,
                    'created_at' => $conversation->created_at->diffForHumans(),
                ]);
            }
            return ApiController::respondWithSuccess($arr);
        }else{
            $errors = [
                'key' => 'my_conversations',
                'value' => trans('messages.noConversations'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function conversations_messages(Request $request , $id)
    {
        $con = Conversation::find($id);
        if ($con)
        {
            $messages = Message::whereConversationId($id)
                ->orderBy('id' , 'asc')
                ->get();
            if ($messages->count() > 0)
            {
                $arr = [];
                foreach ($messages as $message)
                {
                    array_push($arr , [
                        'id'      => $message->id,
                        'room_id' => $con->id,
                        'first_online' => intval($con->first_online),
                        'second_online' => intval($con->second_online),
                        'seen' => intval($con->seen),
                        'user_id' => $message->user_id,
                        'name'    => $message->user->name,
                        'user_photo' => $message->user->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$message->user->photo),
                        'message'    => $message->message == null ? null : $message->message,
                        'file'       => $message->file == null ? null : $message->file,
                        'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                return ApiController::respondWithSuccess($arr);
            }else{
                $errors = [
                    'key' => 'conversations_messages',
                    'value' => trans('messages.noMessages'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $errors = [
                'key' => 'conversations_messages',
                'value' => trans('messages.noConversation'),
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    /**
     * upload image to chat and get link
     * @upload_image
    */
    public function upload_image(Request $request)
    {
        $rules = [
            'image'  => 'required|mimes:jpg,png,jpeg,bmp,gif:max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $file = $request->file('image') == null ? null :UploadImage($request->file('image'), 'photo', '/uploads/chats');
        return ApiController::respondWithSuccess(asset('/uploads/chats/'.$file));
    }
     /**
     *  Get the voice of chat
     * @chat_voice
    */
    public function chat_voice()
    {
        return ApiController::respondWithSuccess(asset('/uploads/voice/voice.mp3'));
    }

}
