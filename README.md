# Gordo-Proxy
        No more anemic data models  :) 
        Gordo is a small library designed to help you build deliciously rich domains.  

## Architecture
Gordo splits the responsibilities of buisness logic and database mapping:
- First doctrine entity which contains your entity data. (the data transfer object).
- A second object which acts as a proxy to your entity, and includes all your business logic. (the business domain model).
- A factory class that allows you to configure and inject dependencies to your proxy object.
- When you have object associations (one-to-one, many-to-one) these will also be transformed into proxy objects.  
- Event listeners are registeres for the setters/getters on you proxy object and syncs the data back to your entity.     

To Summarize:

        With Gordo, create your Proxy object and interact with it as if it were the original entity.

## Quick Start - Creating a proxy
Say we have a User entity, and we want to inject a dependency.

1. Your entity might look like....

        namespace Gordo\Example;
        
        /**
        * @Entity
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
        
2. And a simple password md5 hash checker.  In real life, this might be much more complicated :)    

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

        
2. Create a proxy class .  Be sure to extend your entity.

        namespace Gordo\Example;
        
        class UserProxy extends User 
        {
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

3. To propogate data changes from your proxy back to your original entity add  **EntityDataSyncTrait** to your Proxy class. 

        namespace Gordo\Example;
        
        use Mindgruve\Gordo\Domain\EntityDataSyncTrait;;
        
        class UserProxy extends User 
        {
             use EntityDataSyncTrait;
        }

4. Add an **@ProxyTransform** annotation to your entity to map its proxy class.  The target property is the Fully qualified name of your Proxy class.

        /**
        * @Entity
        * @ProxyTransform(target="Gordo\Example\UserProxy")
        */
        class User {
        }

5. Create a factory class for the UserProxy which contains the logic to build a UserProxy class (and inject your dependencies).  The factory interface has to methods - supports() which should return true if your Factory supports a given class, and build() which is called each time you create a Proxy.

        namespace Gordo\Example;
        
        use Mindgruve\Gordo\Domain\FactoryInterface;
        
        class UserProxyFactory implements FactoryInterface{
            protected $container;
            public function __construct($container){
                $this->container = $container;
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
            $passwordChecker = $this->container->get('password_checker');
    
            return new UserProxy($passwordChecker);
        }    
        
6. Instantiate a Proxy Transformer for your class and register your Factory

        use use Mindgruve\Gordo\Domain\ProxyTransformer;
        use use Gordo\Example\UserProxyFactory;
        
        $userProxyTransformer = new ProxyTransformer('Gordo\Example\User', $entityManager);
        $userProxyTransformer->registerFactory(new UserProxyFactory());

7. Transform your Doctrine entity.

        $userRepository = $entityManager->getRepository('Mindgruve\Example\User');
        $users = $userRepository->findAll();
        $user = $users[0];
        $userProxy = $userProxyTransformer->transform($user);

## Data Syncing between Proxy and Entity
When you add the EntityDataSyncTrait, Gordo registers listeners on the setters and getters of properties of your Proxy, and the add/remove methods for relationships of your entities.  

By default, data is synced automatically from the Proxy --> Entity.

There are a couple of annotations that you can put on your entity to configure this data syncing.

|  Property | Description  | Default  |
|---|---|---|
| syncAuto  | Boolean that controls if automatic syncing enabled  | True (enabled)  |
| syncProperties  | Array of properties to sync  | All properties are synced  |
| syncListeners  | Methods that initiate a sync to entity  | All setters/getters  |

**Example:** Sync all properties, but only when username is updated

    /**
     * @Entity
     * @ProxyTransform(target="Gordo\Example\UserProxy",syncListeners={"setUsername"},syncAuto=true)
     */

**Example:** Sync only the password

    /**
     * @Entity
     * @ProxyTransform(target="Gordo\Example\UserProxy",syncProperties={"passwordHash"},syncAuto=true)
     */

**Example:** Turning off automatic syncing

    /**
     * @Entity
     * @ProxyTransform(target="Gordo\Example\UserProxy",syncAuto=false)
     */


You can also manually update the entity by calling the method syncToEntity() or by accessing the protected property $entity inside your Proxy.








