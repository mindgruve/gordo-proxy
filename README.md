# Gordo

Gordo is a small library designed to help you build rich domain models.  
It was designed to solve a single question:

        How do we include business logic in domain models when using Doctrine?

## Architecture
Gordo splits the responsibilities of buisness logic and database mapping into three classes:
- First doctrine entity which contains your entity data. (a.k.a data transfer object)
- A second object which acts as a proxy to your entity, and includes all your business logic. (a.k.a domain model)
- A factory class (optional) that allows you to configure and inject dependencies to your entity proxy

Gordo will also read your Doctrine annotations transform all of your entity associations to their proxied versions.  Gordo can also be configured to register listeners for the setters/getters on you proxy object and syncs the data back to your entity.  

This allows you to interact with your proxy object __as if__ it were the original entity.  And when you call $entityManager->flush() the updates can be saved back to your persistence storage.

        No more anemic data models  :)  Gordo allows you to build rich data domains.

## Sample Usage: Email Message Encryption
For your client you need to build an email service but the message **and** the message needs to be encrypted.    
To see how Gordo is different, lets see how we build this email service using doctrine.  

First we build the message entity (setters/getters hidden to save space):   

    <?php
    /**
     * @Entity
     */
    class Message
    {

        /**
         * @Id @Column(type="integer")
         * @GeneratedValue
         */
        private $id;

        /** @Column(length=140, name="message") */
        protected $encryptedMessage;
        
        protected $plainTextMessage;

        /** @Column(length=140, name="email") */
        protected $email;

        /**
         * @ManyToOne(targetEntity="Attachment")
         * @JoinColumn(name="attachment_id", referencedColumnName="id")
         */
        protected $attachments;
    }

And the attachment entity:

    /**
    * @Entity
    */
    class Attachment
    {

        /**
         * @Id @Column(type="integer")
         * @ORM\GeneratedValue
         */
        private $id;

        /** @Column(length=140, name="filename") */
        protected $filename;
    }

Then we register an event listener to doctrine so that we encrypt the message before persisting to database (EncryptionService is some object that encapsulates your encryption code)

    <?php
    use Doctrine\ORM\Events;
    use Doctrine\Common\EventSubscriber;
    use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

    class EncyptionListener implements EventSubscriber
    {
        protected $encryptionService;
    
        public function __construct(EncryptionSevice $encryptionService){
            $this->encryptionService
        }
    
        public function getSubscribedEvents()
        {
            return array(
                Events::postUpdate,
            );
        }

        public function postUpdate(LifecycleEventArgs $args)
        {
            $entity = $args->getObject();
            $entityManager = $args->getObjectManager();

            // perhaps you only want to act on some "Product" entity
            if ($entity instanceof Message) {
                if($entity->getPlainTextMessage()){
                    $msg = $entity->getPlainTextMessage();  
                    $entity->setEncryptedMessage($this->encryptionService->encrypt($msg));
                    $entity->setPlainTextMessage(null);
                }
            }
        }

Later you register this event listener...

    $entityManager->getEventManager()->addEventListener(array(Events::preUpdate), new EncyptionListener());   

## If it ain't broke, why fix it?
Using an event listener isn't a bad way of doing it all things considered.  Seriously!  

So whats the problem?  

Hidden dependencies....

See, there is no **explicit** contract in the code that ties the Message object to the EncryptionService.  It is a hidden dependency based on an event listener.  In order for the Message to be correctly saved to the database, it needs its plain text values to be encrypted before it gets saved to disk.

Using Gordo, your dependencies become much more explicit

## Email Mesasge Encryption Example - Using Gordo

