<pre>
    // Database Schema Logic (Laravel Migrations)
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('referral_code');
    $table->integer('parent_id')->nullable(); // Team system ke liye
    $table->decimal('balance', 10, 2)->default(0);
});

Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->decimal('daily_profit', 10, 2);
    $table->integer('validity_days');
});public function buyPlan(Request $request) {
    $user = Auth::user();
    $plan = Plan::find($request->plan_id);

    if ($user->balance >= $plan->price) {
        $user->balance -= $plan->price;
        $user->save();

        Investment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'expiry_date' => now()->addDays($plan->validity_days)
        ]);
        return response()->json(['message' => 'Plan activated!']);
    }
    return response()->json(['message' => 'Insufficient balance!']);
}// App/Console/Commands/DistributeProfit.php
public function handle() {
    $investments = Investment::where('status', 'active')->get();
    
    foreach ($investments as $inv) {
        $user = User::find($inv->user_id);
        $profit = Plan::find($inv->plan_id)->daily_profit;
        
        $user->balance += $profit;
        $user->save();
    }
}<!-- Deposit Modal -->
<div id="depositModal">
    <form action="/deposit/store" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="number" name="amount" placeholder="Amount" required>
        <input type="file" name="proof_image" required>
        <button type="submit">Submit for Approval</button>
    </form>
</div>public function register(Request $request) {
    $parent = User::where('referral_code', $request->ref_code)->first();
    
    User::create([
        'name' => $request->name,
        'parent_id' => $parent->id, // Parent link create ho gaya
    ]);
}</pre>
