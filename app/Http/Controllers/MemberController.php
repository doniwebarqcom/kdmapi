<?php

namespace App\Http\Controllers;

use App\User;
use App\Repositories\CostumePagination;
use App\Transformers\MemberTransformer;
use App\Transformers\MemberPlacePickupTransformer;
use App\Transformers\ListTransactionsTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Support\Facades\Hash;
use Kodami\Models\Mysql\Deposit;
use Kodami\Models\Mysql\Member;
use Kodami\Models\Mysql\MemberPlacePickup;
use Kodami\Models\Mysql\Transaction;
use Kodami\Models\Mysql\Product;
use Kodami\Models\Mysql\RegistrationMemberByPhone;
use Kodami\Models\Mysql\Withdrawal;
use Tymon\JWTAuth\JWTAuth;
use Nexmo\Laravel\Facade\Nexmo;
use Validator;
use DB;

class MemberController extends ApiController
{

    public function register(JWTAuth $JWTAuth)
    {    
    	$rules = [
            'name' 		=> 'required',
            'email' 	=> 'required|email',
            'username' 	=> 'required',
            'password' 	=> 'required|alpha_num|between:6,12',
            'address' 	=> '',
            'phone' 	=> 'required'
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());
		
        $cekMember = Member::where('email', $this->request->get('email'))->first();
        if ($cekMember)
            return $this->response()->error('User already exists', 409);		

		$member = new Member;
		$member->name	= $this->request->get('name');
		$member->email	= $this->request->get('email');
		$member->username	= $this->request->get('username');
		$member->password	= Hash::make($this->request->get('password'));
		$member->address	= $this->request->get('address');
		$member->phone	= $this->request->get('phone');

		if(! $member->save())
			return $this->response()->error("failed save data");
    	
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function product_validated(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->fromUser($member);
        if(! $member->shop)
            return $this->response()->error("User dont have koprasi");

        $q = $this->request->get('query') ? $this->request->get('query') : null;
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        $post = Product::where('koprasi_id', $member->shop->id)->whereNotNull('kodami_product_id')->where('is_validate', 1)->paginate($limit);
        $pagination = new CostumePagination($post);     
        $result = $pagination->render();

        return $this->response()->success($result['data'], ['meta.token' => $token, 'paging' => $result['paging']] , 200, new ProductTransformer(), 'collection');
    }

    public function isi_saldo(JWTAuth $JWTAuth)
    {
        $rules = [
            'saldo'      => 'required',
            'type'     => 'required',
            'type_transaction'  => 'required'
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());
        
        $type = $this->request->get('type');        
        $member =  $JWTAuth->parseToken()->authenticate();                

        if(! ($type != 1 OR $type != 2))
            return $this->response()->error('Wrong Type');

        $status = 0;
        if($type == 2)
        {
            $dana = 0;

            if(! is_null($member->user_id))
            {
                $in = Deposit::groupBy('user_id')
                        ->selectRaw('sum(nominal) as sum')
                        ->where('user_id', $member->user_id)
                        ->where('status', 1)
                        ->first();
                $total_in = $in->sum ? $in->sum : 0;

                $out = Withdrawal::groupBy('user_id')
                        ->selectRaw('sum(nominal) as sum')
                        ->where('user_id', $member->user_id)
                        ->where('status', 1)
                        ->first();

                $total_out = $out->sum ? $out->sum : 0;

                $dana = $total_in - $total_out;
            }else
                return $this->response()->error('Not Member of Kodami');                
            
            if($dana < $this->request->get('saldo'))
                return $this->response()->error("Saldo Member's Koprasi not enougth");

            $no_invoice  = (Withdrawal::count()+1).$member->user_id.'/INV/KDM/'. date('d').date('m').date('y');

            $withdrawal = new Withdrawal;
            $withdrawal->no_invoice = $no_invoice;
            $withdrawal->user_id = $member->user_id;
            $withdrawal->nominal = (int) $this->request->get('saldo');
            $withdrawal->status = 1;
            $withdrawal->type = $this->request->get('saldo');
            $withdrawal->save();

            $status = 2;
        }
        
        $total_transaction = (int) (DB::table('transactions')->count() + 1);
        $transaction_code = random_trasaction_code($total_transaction);

        $transaction = new Transaction;        
        $transaction->member_id = $member->id;
        $transaction->transaction_code = $transaction_code;
        $transaction->status = $status;
        $transaction->fee_random = quickRandomNumber();
        $transaction->price_product = $this->request->get('saldo');
        $transaction->type_transaction = $this->request->get('type_transaction');
        $transaction->save();

        if($type == 2)
        {
            $member->saldo += $this->request->get('saldo');
            $member->save();
        }

        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($transaction, ['meta.token' => $token] , 200, new ListTransactionsTransformer());
    }

    public function pending_top_up(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->fromUser($member);
        $transaction = Transaction::where('member_id', $member->id)->where('type_transaction', 2)->where('status', 0)->get();
        return $this->response()->success($transaction, ['meta.token' => $token] , 200, new ListTransactionsTransformer(), 'collection');
    }

    public function list_transaction(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->fromUser($member);
        $transaction = Transaction::where('member_id', $member->id)->where('type_transaction', 1)->get();
        return $this->response()->success($transaction, ['meta.token' => $token] , 200, new ListTransactionsTransformer(), 'collection', null, ['items']);
    }

    public function detail_transaction($transaction_code, JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->fromUser($member);
        $transaction = Transaction::where('member_id', $member->id)->where('transaction_code', $transaction_code)->first();

        return $this->response()->success($transaction, ['meta.token' => $token] , 200, new ListTransactionsTransformer(), 'item', null, ['items']);
    }

    public function login(JWTAuth $JWTAuth)
    {
    	$email = $this->request->get('email');
    	$username = $this->request->get('username');

    	if( $username == "" AND $email == "")
    		return $this->response()->error("email or username cant be null");

    	$rules = [
                'password' 	=> 'required',
        ];

    	$validator = Validator::make(
    		$this->request->all(),
    		$rules
		);

		if ($validator->fails())
			return $this->response()->error($validator->errors()->all());

		$password = $this->request->get('password');
		$member = "";		
		
        if($username == "" )
			$member = Member::where('email', $email)->first();
        elseif($email == "" )
		  $member = Member::Where('username', $username)->first();

		if( ! $member OR ! (Hash::check($password, $member->password)))
			return $this->response()->error("Wrong username or email or password");

		$token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function getUser(JWTAuth $JWTAuth)
    {   
        $user =  $JWTAuth->parseToken()->authenticate();
        $token = $JWTAuth->getToken();
        return $this->response()->success($user, ['meta.token' => (string) $token] , 200, new MemberTransformer(), 'item');
    }

    public function phone()
    {
        $rules = [
            'phone'      => 'required'
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))->first();
        if(! $register)
            $register = new RegistrationMemberByPhone;

        $phone = $this->request->get('phone');
        $newtimestamp = strtotime(date("Y-m-d h:i:s").' + 5 minute');
        $finalDate =  date('Y-m-d H:i:s', $newtimestamp);
        $register->phone_number = $phone;
        $register->unique_code = quickRandom(6);
        $register->expired_code = $finalDate;

        if(! $register->save())
            return $this->response()->error("error at saving data");

        $send_sms = Nexmo::message()->send([
            'to'   => $this->request->get('phone'),
            'from' => '6282134916615',
            'text' => "This is your code : ".$register->unique_code."   powered by"
        ]);

        if(! $send_sms)
            return $this->response()->error("error at sending sms");

        return $this->response()->success('succes');
    }

    public function cekCode(JWTAuth $JWTAuth)
    {
        $rules = [
            'phone' => 'required',
            'code'  => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $newtimestamp = strtotime(date("Y-m-d h:i:s").' + 5 minute');
        $finalDate =  date('Y-m-d H:i:s', $newtimestamp);

        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))
                    ->where('unique_code', $this->request->get('code'))
                    ->where('expired_code', '<=', $finalDate)
                    ->first();

        if(! isset($register))
            return $this->response()->error("data not found");

        $member = Member::where('phone', $this->request->get('phone'))->first();
        $isMember = 0;
        $header = [];
        if(isset($member)){
            $isMember = 1;
            $token = $JWTAuth->fromUser($member);
            $header['meta.token'] = $token;
        }

        return $this->response()->success(["member" => $isMember], ['meta.token' => $token]);
    }

    public function registerByPhone(JWTAuth $JWTAuth)
    {
        $register = RegistrationMemberByPhone::where('phone_number', $this->request->get('phone'))
                    ->where('unique_code', $this->request->get('code'))
                    ->first();

        $member = Member::where('phone', $this->request->get('phone'))->first();
        if(! isset($member) AND isset($register) )
        {
            $member = new Member;
            $member->name   = "kodami_".$this->request->get('phone');
            $member->email  = $this->request->get('phone')."@mail.com";
            $member->username   = "kodami_".$this->request->get('phone');
            $member->password   = Hash::make($this->request->get('password'));
            $member->address    = "";
            $member->phone  = $this->request->get('phone');

            $member->save();            
        } else if(! isset($member) AND ! isset($register) )
            return $this->response()->error("data not found");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function dana_simpanan_anggota(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        if(is_null($member->user_id))
            return $this->response()->success(['dana' => 0]);
        
        $in = Deposit::groupBy('user_id')
                ->selectRaw('sum(nominal) as sum')
                ->where('user_id', $member->user_id)
                ->where('status', 1)
                ->first();
        $total_in = $in->sum ? $in->sum : 0;

        $out = Withdrawal::groupBy('user_id')
                ->selectRaw('sum(nominal) as sum')
                ->where('user_id', $member->user_id)
                ->where('status', 1)
                ->first();

        $total_out = $out->sum ? $out->sum : 0;

        $dana = $total_in - $total_out;
        return $this->response()->success(['dana' => $dana]);
    }

    public function login_by_anggota(JWTAuth $JWTAuth)
    {
        $rules = [
            'password' => 'required',
            'no_anggota'  => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $user = User::where('no_anggota',  $this->request->get('no_anggota'))->first();

        $password = $this->request->get('password');
        
        if( ! $user OR ! (Hash::check($password, $user->password)))
            return $this->response()->error("Wrong No Anggota or Password");

        $member = Member::where('user_id', $user->id)->orWhere('email' , $user->email)->first();

        if($member)
        {
            $member->email = $user->email;
            $member->user_id = $user->id;
        } else {
            $member = new Member;
            $member->name   = $user->name;
            $member->user_id = $user->id;
            $member->email  = $user->email;
            $member->username   = $user->no_anggota;
            $member->password   = Hash::make($password);
            $member->address    = "";
            $member->phone  = $user->telepon ? $user->telepon : $user->no_anggota;
        }

        $member->save();

        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function upload_image(JWTAuth $JWTAuth)
    {
        $rules = [
            'photo' => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
        $member->image  = $this->request->get('photo');

        if(! $member->save())
            return $this->response()->error("failed save data");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function profile_store(JWTAuth $JWTAuth)
    {
        $rules = [
            'name' => 'required',
            'birth' => 'required',
            'gender' => 'required',
        ];

        $validator = Validator::make(
            $this->request->all(),
            $rules
        );

        if ($validator->fails())
            return $this->response()->error($validator->errors()->all());

        $member =  $JWTAuth->parseToken()->authenticate();
        $member->name  = $this->request->get('name');
        $member->gender  = $this->request->get('gender');
        $member->birth  = date("Y-m-d" , $this->request->get('birth'));

        if(! $member->save())
            return $this->response()->error("failed save data");
        
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($member, ['meta.token' => $token] , 200, new MemberTransformer(), 'item');
    }

    public function place_list(JWTAuth $JWTAuth)
    {
        $member =  $JWTAuth->parseToken()->authenticate();
        $pickup = MemberPlacePickup::where('member_id', $member->id)->get();
        $token = $JWTAuth->fromUser($member);
        return $this->response()->success($pickup, ['meta.token' => $token] , 200, new MemberPlacePickupTransformer(), 'collection');
    }
}