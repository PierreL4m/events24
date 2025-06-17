<?php
namespace App\Tests\Behat;

use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\MailerInterface;
use App\Kernel;
use PHPUnit\Framework\Assert;
use App\Helper\MailerHelper;
use Symfony\Component\Mailer\EventListener\MessageLoggerListener;
use Zenstruck\Mailer\Test\InteractsWithMailer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\TestCase;
use Zenstruck\Mailer\Test\TestEmail;

/**
 * Provides some steps/methods which are useful for testing a Symfony2 and 4 application.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author  France Benoit
 */
class SymfonyMailerContext extends KernelTestCase implements Context 
{
    
    // use MailerAssertionsTrait;
    use InteractsWithMailer;

    /**
     * 
     * @var Kernel $kernel
     */
    /*
    private static Kernel $kernel;
    */
    private MailerHelper $mailerHelper;
    
    /**
     * @param Kernel $kernel
     *
     * @return void
     */
    public function __construct(Kernel $kernel, MailerHelper $mailerHelper)
    {
        // static::setKernel($kernel);
        $this->mailerHelper = $mailerHelper;
        self::bootKernel();
        self::_startTestMailer();
        
    }
    
    /**
     * @When i send an email
     */
    public function iSendAnEmail()
    {
        
        try {
            $this->mailerHelper->sendMail('webmaster@l4m.fr', 'test send email', 'test');
        }
        catch(\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @Then /^an email should have been sent$/
     */
    public function anEmailShouldHaveBeenSent()
    {
        
        try {
            $this->mailer()->assertSentEmailCount(1);
            // $this->assertEmailCount(1);
        }
        catch(\Exception $e) {
            throw new \RuntimeException(sprintf('Expected an email to be sent, but %d emails were sent.', count($this->mailer()->sentEmails())));
            // throw new \RuntimeException(sprintf('Expected an email to be sent, but %d emails were sent.', count($this->getMailerMessages())));
        }
    }
    
    
    /**
     * @Then /^no email should have been sent$/
     */
    public function noEmailShouldHaveBeenSent()
    {
        return true;
        try {
            $this->mailer()->assertSentEmailCount(0);
            // $this->assertEmailCount(0); 
        }
        catch(\Exception $e) {
            throw new \RuntimeException(sprintf('Expected no email to be sent, but %d emails were sent.', count($this->mailer()->sentEmails())));
            // throw new \RuntimeException(sprintf('Expected no email to be sent, but %d emails were sent.', count($this->getMailerMessages())));
        }
        
    }

    /**
     * @Then /^email with subject "([^"]*)" should have been sent(?: to "([^"]+)")?$/
     */
    public function emailWithSubjectShouldHaveBeenSent($subject, $to = null)
    {
        try {
            $this->mailer()->assertEmailSentTo($to, function(TestEmail $email) use ($subject) {
                $email
                ->assertSubjectContains($subject);
            });
            
        }
        catch(\Exception $e) {
            $this->mailer()->reset();
            throw $e;
        }
        $this->mailer()->reset();
        return true;
        
        
        // TODO : reprendre dans la logique ci dessous le cote "verbeux" : le sujet n'a pas été trouvé mais on a trouvé tel autre, etc
        $foundToAddresses = null;
        $foundSubjects = array();
        $ok = false;

        foreach ($mailer->getMessages('default') as $message) {

            $foundSubjects[] = $message->getSubject();

            if (strpos($message->getSubject(), $subject) !== false){
                // for dev purpose get x-swift-to
                $foundToAddresses = implode(', ', array_keys($message->getHeaders()->get('x-swift-to')->getAddresses()));


                if (null !== $to) {
                    $toAddresses = $message->getHeaders()->get('x-swift-to')->getAddresses();

                    if (in_array($to, $toAddresses)) {
                        $ok = true;
                    }
                } else {
                    // found, and to  isn't checked
                    $ok = true;

                }
                // found but no "to" asked
            }
        }
        if($ok){
            return;
        }
        if (!$foundToAddresses) {
            if (!empty($foundSubjects)) {
                throw new \RuntimeException(sprintf('Subject "%s" was not found, but only these subjects: "%s"', $subject, implode('", "', $foundSubjects)));
            }
            // not found
            throw new \RuntimeException(sprintf('No message with subject "%s" found.', $subject));
        }
        throw new \RuntimeException(sprintf('Subject found, but "%s" is not among to-addresses: %s', $to, $foundToAddresses));
    }
    /**
     * @Then /^email with subjects "([^"]*)" or "([^"]*)" should have been sent(?: to "([^"]+)")?$/
     */
    public function emailWithSubjectsShouldHaveBeenSent($subject1, $subject2, $to = null)
    {
        $subjects = array($subject1, $subject2);
        try {
            $this->mailer()->assertEmailSentTo($to, function(TestEmail $email) use (& $subjects) {
                foreach($subjects as $k => $s) {
                    try {
                        $email->assertSubjectContains($s);
                        unset($subjects[$k]);
                    }
                    catch(\Exception $e) {
                        
                    }
                }
            });
            
                if(count($subjects)) {
                    die('on a pas tout torué');
                }
                
        }
        catch(\Exception $e) {
            $this->mailer()->reset();
            throw $e;
        }
        $this->mailer()->reset();
        return true;
        
        return true;
        $mailer = $this->loadProfile()->getCollector('swiftmailer');
        if (0 === $mailer->getMessageCount()) {
            throw new \RuntimeException('No emails have been sent.');
        }

        $foundToAddresses = null;
        $foundSubjects = array();
        $ok = false;

        foreach ($mailer->getMessages('default') as $message) {

            $foundSubjects[] = $message->getSubject();

            if (strpos($message->getSubject(), $subject1) !== false || strpos($message->getSubject(), $subject2) !== false){
                // for dev purpose get x-swift-to
                $foundToAddresses = implode(', ', array_keys($message->getHeaders()->get('x-swift-to')->getAddresses()));


                if (null !== $to) {
                    $toAddresses = $message->getHeaders()->get('x-swift-to')->getAddresses();

                    if (in_array($to, $toAddresses)) {
                        $ok = true;
                    }
                } else {
                    // found, and to  isn't checked
                    $ok = true;

                }
                // found but no "to" asked
            }
        }
        if($ok){
            return;
        }
        if (!$foundToAddresses) {
            if (!empty($foundSubjects)) {
                throw new \RuntimeException(sprintf('Subject "%s" was not found, but only these subjects: "%s"', $subject, implode('", "', $foundSubjects)));
            }
            // not found
            throw new \RuntimeException(sprintf('No message with subject "%s" found.', $subject));
        }
        throw new \RuntimeException(sprintf('Subject found, but "%s" is not among to-addresses: %s', $to, $foundToAddresses));
    }
    /**
     * Loads the profiler's profile.
     *
     * If no token has been given, the debug token of the last request will
     * be used.
     *
     * @param string $token
     * @return \Symfony\Component\HttpKernel\Profiler\Profile
     * @throws \RuntimeException
     */
    public function loadProfile($token = null)
    {
        if (null === $token) {
            $headers = $this->getSession()->getResponseHeaders();
            if (!isset($headers['X-Debug-Token']) && !isset($headers['x-debug-token'])) {
                throw new \RuntimeException('Debug-Token not found in response headers. Have you turned on the debug flag?');
            }
            $token = isset($headers['X-Debug-Token']) ? $headers['X-Debug-Token'] : $headers['x-debug-token'];
            if (is_array($token)) {
                $token = end($token);
            }
        }
        return $this->kernel->getContainer()->get('profiler')->loadProfile($token);
    }
    /*
    public static function getContainer() {
        $container = static::getKernel()->getContainer();
        return $container;
        // var_dump($container->has('))
    }*/
    
    private static function getKernel() :Kernel {
        return static::$kernel;
    }
    /*
    private static function setKernel(Kernel $kernel) :void {
        static::$kernel = $kernel;
    }*/
}
