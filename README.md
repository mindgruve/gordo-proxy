# Gordo-Proxy
        No more anemic data models  :) 
        Gordo is a small library designed to help you build deliciously rich domains.  

## Architecture
Gordo splits the responsibilities of business logic from your database mapping:   
- First a doctrine data object. (Entity if using ORM, Document if using ODM).   
- A second object which holds most of your business logic which acts as a proxy to your doctrine data object.   
- A factory class for dependency injection.    
- When you have object associations (one-to-one, many-to-one) these will also be lazy-loaded and into proxy objects.     
- You can configure your Proxy object to sync data back to your doctrine data object.      

To Summarize:

        With Gordo you can create a Proxy object and interact with it as if it were the original data object.

## Quick Start - Creating a proxy
Say we have a User data object, and we want to inject a dependency (like an encryption service).

1. Your doctrine entity is updated by adding an annotation @Proxy, where the target is the fully qualified name of your Proxy object.

        namespace Gordo\Example;
        
        /**
        * @Entity
        * @Proxy(target="Gordo\Example\UserProxy")
        */
        class User 
        {
            /**
             * @Id @Column(type="integer")
             * @GeneratedValue
             */
            private $id;
    
            /** @Column(length=140) */
            protected $username;       
            
             /** @Column(length=140) */
            protected $passwordHash;    
        }
        
2. Here is the service you want to inject.  In real life, this might be much more complicated :)    

        Namespace Gordo\Example;
        
        class passwordChecker
        {
            public function isValid($password, $hash){
                return md5($password) == $hash;
            }
        
            public function hash($password){
                return md5($password);
            }
        }

        
2. Since we can't inject dependencies into our Doctrine entity class, we create a proxy class .  There are two requirements: (1) Be sure to extend your entity and (2) use the ProxyTrait.  In this example, our UserProxy also encapsulates our encryption business logic.

        namespace Gordo\Example;
        use Mindgruve\Gordo\Traits\ProxyTrait;;
        
        class UserProxy extends User 
        {
             use ProxyTrait;
        
            protected $passwordChecker;
            
            public function __construct($passwordChecker){
                $this->passwordChecker = $passwordChecker;
            }
            
            public function updatePassword($password){
                $this->passwordHash = $this->passwordChecker->hash($password);
            }
            
            public function isValidPassword($password){
                return $this->passwordHash->isValid($password);
            }
        }
        
6. Gordo-Proxy allows you to register a factory to create your Proxy objects.  Be sure to implement  **Mindgruve\Gordo\Proxy\FactoryInterface**.  The factory interface has to methods - supports() which should return true if your Factory supports a given class, and build() which should return a new instance of your Proxy class.  (For example, If this factory were used in Symfony it would be a service, and the encryption service would be injected.)

        namespace Gordo\Example;
        
        use Mindgruve\Gordo\Proxy\FactoryInterface;
        
        class UserProxyFactory implements FactoryInterface{
            protected $passwordChecker;
            public function __construct($passwordChecker){
                $this->passwordChecker = $passwordChecker;
            }
            
            public function supports($proxyClass){
                if ($proxyClass == 'Gordo\Example\UserProxy') {
                    return true;
                }

                return false;
            }
        }
        
        /**
         * @param $proxyClass
         * @return object
         */
        public function build($proxyClass)
        {
            return new UserProxy($this->passwordChecker);
        }    
        
6. Now you are ready to register your factory with the ProxyManager and build your first proxy...

        use Mindgruve\Gordo\Proxy\ProxyManager;
        use Gordo\Example\UserProxyFactory;
        
        // Register the factory
        $proxyManager = new ProxyManager($entityManager);
        $userManager->registerFactory(new UserProxyFactory());
        
        // Get a doctrine entity
        $userRepository = $entityManager->getRepository('Mindgruve\Example\User');
        $users = $userRepository->load($id); 
        
        // Transform doctrine entity into proxy
        $userProxy = $proxyManager->transform($user);

## Automatic Data Syncing between Proxy --> Data Object
When you create a Proxy, Gordo-Proxy copies over the data from the Doctrine object into the Proxy when created.  From this point on, the doctrine object and the proxy generated by Gordo-Proxy are separate.

However, sometimes, it is useful to establish data binding from the proxy back to the original doctrine entity or document.

There are a couple of annotations that you can put on your entity to configure this data syncing.

|  Property | Description  | Default  |
|---|---|---|
| syncProperties  | Array of properties to sync  | All properties are synced  |
| syncMethods  | Methods that initiate a sync to entity  | By default, methods sync data  |

If you want to sync all properties use syncProperties={"*"}   
Likewise If you want to sync all the methods use syncMethods={"*"}.     
If syncMethods is null or empty array, then data syncing is disabled.


**Example:** Sync all properties, but only when username is updated

    /**
     * @Entity
     * @Proxy(target="Gordo\Example\UserProxy",syncMethods={"setUsername"})
     */

**Example:** Sync only the password

    /**
     * @Entity
     * @Proxy(target="Gordo\Example\UserProxy",syncProperties={"passwordHash"},syncMethods={"setPassword"})
     */

**Example:** Turning on automatic syncing for all properties

    /**
     * @Entity
     * @Proxy(target="Gordo\Example\UserProxy",syncMethods={"*"})
     */

## Manually Syncing Data
Part of the ProxyTrait is a public function syncData($syncDirection, $properties).    

The first parameter **$syncDirection** is either:   
**ProxyConstants::UPDATE_DATA_OBJECT** - which will transfer the changes from the proxy object --> doctrine data object  
**ProxyConstants::UPDATE_PROXY** - which will transfer changes the other way and pull up changes from the doctrine data object --> proxy

The second parameter is either an array of properties to sync or one of 3 constants:  
**ProxyConstants::SYNC_PROPERTIES_NONE** - which is a noop   
**ProxyConstants::SYNC_PROPERTIES_DEFAULT** - which will sync properties defined in the objects @Proxy meta data for syncProperties   
**ProxyConstants::SYNC_PROPERTIES_ALL** - which will sync all the properties (regardless of what is in the @Proxy meta data)

**Note** There is also a protected method getDataObject() as part of the ProxyTrait which will return the original data object used to create the proxy.

## Todo
- Build command to build production ready proxies.





