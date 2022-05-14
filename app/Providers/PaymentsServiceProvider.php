<?php

namespace App\Providers;

use App\Model\Transaction;
use App\Model\Withdrawal;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get subscription monthly interval
     *
     * @param $transactionType
     * @return int
     */
    public static function getSubscriptionMonthlyIntervalByTransactionType($transactionType)
    {
        $interval = 1;
        if ($transactionType != null) {
            switch ($transactionType) {
                case Transaction::YEARLY_SUBSCRIPTION:
                    $interval = 12;
                    break;
                case Transaction::THREE_MONTHS_SUBSCRIPTION:
                    $interval = 3;
                    break;
                case Transaction::SIX_MONTHS_SUBSCRIPTION:
                    $interval = 6;
                    break;
                default:
                    $interval = 1;
                    break;
            }
        }

        return $interval;
    }

    /**
     * Get withdrawal limit amounts
     * @return string
     */
    public static function getWithdrawalAmountLimitations()
    {
        $withdrawalsMinAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . '20';
        if (getSetting('withdrawals-deposit.withdrawal_min_amount') != null && getSetting('withdrawals-deposit.withdrawal_min_amount') > 0) {
            $withdrawalsMinAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . getSetting('withdrawals-deposit.withdrawal_min_amount');
        }
        $withdrawalsMaxAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . '500';
        if (getSetting('withdrawals-deposit.withdrawal_max_amount') != null && getSetting('withdrawals-deposit.withdrawal_max_amount') > 0) {
            $withdrawalsMaxAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . getSetting('withdrawals-deposit.withdrawal_max_amount');
        }

        return __('Amount').' ('.$withdrawalsMinAmount.' min, '.$withdrawalsMaxAmount.' max)';
    }

    /**
     * Get deposit limit amounts
     * @return string
     */
    public static function getDepositLimitAmounts()
    {
        $depositMinAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . '5';
        if (getSetting('withdrawals-deposit.deposit_min_amount') != null && getSetting('withdrawals-deposit.deposit_min_amount') > 0) {
            $depositMinAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . getSetting('withdrawals-deposit.deposit_min_amount');
        }
        $depositMaxAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . '500';
        if (getSetting('withdrawals-deposit.deposit_max_amount') != null && getSetting('withdrawals-deposit.deposit_max_amount') > 0) {
            $depositMaxAmount = SettingsServiceProvider::getWebsiteCurrencySymbol() . getSetting('withdrawals-deposit.deposit_max_amount');
        }

        return __('Amount').' ('.$depositMinAmount.' min, '.$depositMaxAmount.' max)';
    }

    /**
     * Get withdrawals minimum amount
     * @return \Illuminate\Config\Repository|int|mixed|null
     */
    public static function getWithdrawalMinimumAmount(){
        return
            getSetting('withdrawals-deposit.withdrawal_min_amount') != null
            && getSetting('withdrawals-deposit.withdrawal_min_amount') > 0
                ? getSetting('withdrawals-deposit.withdrawal_min_amount') : 20;
    }

    /**
     * Get withdrawals maximum amount
     * @return \Illuminate\Config\Repository|int|mixed|null
     */
    public static function getWithdrawalMaximumAmount(){
        return
            getSetting('withdrawals-deposit.withdrawal_max_amount') != null
            && getSetting('withdrawals-deposit.withdrawal_max_amount') > 0
                ? getSetting('withdrawals-deposit.withdrawal_max_amount') : 500;
    }

    /**
     * Get deposit minimum amount
     * @return \Illuminate\Config\Repository|int|mixed|null
     */
    public static function getDepositMinimumAmount(){
        return
            getSetting('withdrawals-deposit.deposit_min_amount') != null
            && getSetting('withdrawals-deposit.deposit_min_amount') > 0
                ? getSetting('withdrawals-deposit.deposit_min_amount') : 5;
    }

    /**
     * Get deposit maximum amount
     * @return \Illuminate\Config\Repository|int|mixed|null
     */
    public static function getDepositMaximumAmount(){
        return
            getSetting('withdrawals-deposit.deposit_max_amount') != null
            && getSetting('withdrawals-deposit.deposit_max_amount') > 0
                ? getSetting('withdrawals-deposit.deposit_max_amount') : 500;
    }

    /**
     * Creates transaction for an approved withdrawal
     * @param $withdrawal
     */
    public static function createTransactionForWithdrawal($withdrawal){
        try{
            if($withdrawal->status === Withdrawal::APPROVED_STATUS){
                $data = [];
                $data['recipient_user_id'] = $withdrawal->user_id;
                $data['sender_user_id'] = Auth::user()->id;
                $data['type'] = Transaction::WITHDRAWAL_TYPE;
                $data['amount'] = $withdrawal->amount;
                $data['payment_provider'] = Transaction::MANUAL_PROVIDER;
                $data['currency'] = SettingsServiceProvider::getAppCurrencyCode();
                $data['status'] = Transaction::APPROVED_STATUS;

                Transaction::create($data);
            }
        } catch (\Exception $e){
        }
    }

    /**
     * Fetch withdrawals allowed payment methods from admin panel
     * @return array
     */
    public static function getWithdrawalsAllowedPaymentMethods() {
        $allowedPaymentMethods = [];
        if(getSetting('withdrawals-deposit.withdrawal_payment_methods')) {
            $allowedPaymentMethods = explode(', ', getSetting('withdrawals-deposit.withdrawal_payment_methods'));
        }

        // adds a default value in case there is nothing set in admin panel
        if(empty($allowedPaymentMethods)){
            $allowedPaymentMethods[] = 'Other';
        }

        return $allowedPaymentMethods;
    }
}
