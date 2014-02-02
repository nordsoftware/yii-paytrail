<?php
/**
 * PaytrailController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.controllers
 */

class PaytrailController extends PaymentController
{
    /**
     * @var string
     */
    public $managerId = 'payment';

    public function actionTest()
    {
        $transaction = PaymentTransaction::create(
            array(
                'gateway' => 'paytrail',
                'orderIdentifier' => 1,
                'description' => 'Test payment',
                'price' => 100.00,
                'currency' => 'EUR',
                'vat' => 28.00,
            )
        );

        $transaction->addShippingContact(
            array(
                'firstName' => 'Foo',
                'lastName' => 'Bar',
                'email' => 'foo@bar.com',
                'phoneNumber' => '1234567890',
                'mobileNumber' => '0400123123',
                'companyName' => 'Test company',
                'streetAddress' => 'Test street 1',
                'postalCode' => '12345',
                'postOffice' => 'Helsinki',
                'countryCode' => 'FIN',
            )
        );

        $transaction->addItem(
            array(
                'description' => 'Test product',
                'code' => '01234',
                'quantity' => 5,
                'price' => 19.90,
                'vat' => 23.00,
                'discount' => 10.00,
                'type' => 1,
            )
        );

        $transaction->addItem(
            array(
                'description' => 'Another test product',
                'code' => '43210',
                'quantity' => 1,
                'price' => 49.90,
                'vat' => 23.00,
                'discount' => 50.00,
                'type' => 1,
            )
        );

        Yii::app()->payment->startTransaction($transaction);
    }

    /**
     * @param int $transactionId
     */
    public function actionSuccess($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_SUCCESSFUL, $transaction);
        $this->redirect($manager->successUrl);
    }

    /**
     * @param int $transactionId
     */
    public function actionFailure($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_FAILED, $transaction);
        $this->redirect($manager->failureUrl);
    }

    /**
     * @param int $transactionId
     */
    public function actionNotify($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_COMPLETED, $transaction);
        Yii::app()->end();
    }

    /**
     * @param int $transactionId
     */
    public function actionPending($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_PENDING, $transaction);
        Yii::app()->end();
    }
}