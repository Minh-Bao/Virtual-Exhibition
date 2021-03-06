## Description d'un Pin

    -Title
    -Description
    -Image
    -Auteur
    -Comment (?)

### Description de la table pins

    -id
    -title
    -description
    -createdAt
    -updatedAt
    -imageFile


## A faire/ A voir

-Pour resize l'image si le thumbnail ne veut pas s'afficher  et creer une erreur type memory-limit.... utiliser imagick


-nvm est un outil pour installer et utiliser a l'envie les différentes version de node.js

-déployer gratuitement son appli avec Heroku: 
cf:"https://www.heroku.com/"

-installer npm ou yard cf: node.js


-Extension Twig...... (Creer des méthodes dans le fichier "src/Twig/appExtension.php" et les appeler depuis les fichier .twig)
-Http request methods
-A voir redirectToRoute();
-Alias Symfony console sc dans le terminal
-Customiser les pages d'erreur(cf: symfony.com)
-Check page https://www.doctrine-project.org/ pour se mettre a jour sur Doctrine.

/!\ Attention lorsque vous mettez à jour vos recettes
voir ses recette en cours : cmd "symfony recipe"

Voir EntityManagerInterface....

    Pour persist et flush on a besoin de l'EntityManagerInterface en faisant injection de dépendance(*). function(EntityManagerInterface $em)

    Cela revient a faire : $em = $this->getDoctrine()->getManager();
        getDoctrine() //recupère Doctrine au niveau de container
        getManager() // recupère EntityManager

(*)Injection de dépendance: 
    la commande " symfony console debug:autowiring + (terme)" permets de voir tout ce qu'on peut injecter ou une injection en particulier .
### Doctrine

    Installer le bundle doctrine.
    Creer une Database avec la cmd [symfony console ].


#### Creation/modif d'entité (Entity)

    Executer la cmd [symfony console make:entity (nomDeLentité)]
    (...) y/N? ->Yes
    Puis entrer le nom des champs le type ect...
    Puis [enter] pour valider

    Cela crée un fichier "src/Entity/(nomDeLentité).php", avec ses annotations ainsi que des méthodes 
    Setter et Getter.

    Les différentes class et propriete attributs de l'entité seront en générale mappé avec la BDD.

    Enfin la commande crée automatique une class repository et ses fichiers. 
    ex: "src/Repository/(nomDeLentité)Repository.php"

    Et met a jour le fichier "config.yml"

    On peut mettre a jour l'entité avec la même commande .

##### Creation d'une relation entre deux tables:

    Lors de la creation d'une entité puis d'un champ on peut y mettre comme type au lieu de string apr exemple 
    "relation" puis suivre les instruction pour y mettre le type de relation ManyToMany, ManyToOne etc....

    /!\Lors de la migration de la BDD [symfony doctrine:migrations:migrate]
    Il est possible qu'il y ait conflit car des champ on deja été crée et que ces champs rentre en conflit avec ceux que l'on veut creer.
    Dans ce cas on peut supprimer ou vider la table problématique et recommencer la procédure.
    -> [symfony console doctrine:database:create]
    -> [symfony doctrine:migrations:migrate]
    Grace a nos fichier de migrations toujours en place on va recreer les tables vierge avec les bonnes relations


#### Migration

    Executer la cmd [symfony console make:migration]
    Un fichier Version(DateEtHeureDuFichier).php dans le dossier /migrations
    Ce fichier contient les requetes sql pour modifier la BDD.
    Dans la méthode getDescription() on peut ajouter un résumé de l'action du fichier.
    
    ex: function getDescription(){}return 'Add image_name field to (nomDeLentité) table';

    /!\ A noter la notation pour les attributs de l'entité se font en camelCase par contre elle seront traduites dans la BDD en snake_case
    cf: fichier doctrine.yaml dans config/packages.

    Enfin si tout est ok appliquer la migration avec la cmd [symfony console doctrine:migrations:migrate]

    (y/n?)->yes

#### Se connecter a la BDD avec le Terminal pour vérifer que tout la migration a bien été faites.

    Exécuter la cmd [mysl -u root] si pas de mdp, 
    Sinon [mysql -u (user) -p] puis entrer le mdp a sa demande.

    puis les habituelles requetes sql pour agir sur la bdd.

    /!\ lors de la creation de la table, on peut mettre un champ avec une contrainte d'unicité afin de ne pas avoir deux fois la même valeur a un champ (ex: 2 fois la même adresse mail on verra ainsi dans l'entité User un attribut mail avec une valeur "unique = true")


### Pour valider les changements dans Git

    Executer les cmd suivantes:

    [git status]
    [git add -A]
    [git commit -m "message de commit"]


### Les Formulaires


    Eviter de mettre des boutons de type submit 
    -> mettre les bouton au niveau de la "vue"
    form builder-> form -> form view

Dans le controller:

```php
public function create(): Response
    {
        $form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        return $this->render('pins/create.html.twig', [
            'variable' => $form->createView()
        ]);
    }
``` 

    en twig: exemple 

 ```html

    {{ form_start(variable) }} <-debut du formulaire->

        {{ form_widget(variable) }} <-Les champs->

        <input type="submit" value="Create Pin" > <-bouton de soumission->

    {{ form_end(variable) }}<-fin du formulaire->

```

Un peu de refactoring: Dans ce cas $data est un tableau....

```php
$form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); 

            $pin = new Pin;

            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);

            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');

        }
``` 

Mais on peut aussi creer un instance de new Pin en paramètre de createFormbuilder, alors $data se comporte comme un objet. 
On peut donc:

```php
$form = $this->createFormBuilder(new Pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); 

            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('app_home');

        }
``` 

Ou encore en mettant l'instance de Pin dans une variable cette dernière ce comportera de la même maniere que " $form->getData() " 

```php
$pin= new Pin;

$form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');

        }
``` 
    Pour delete une entree dans la bdd via un bouton et une requete DELETE, il faut faire un formulaire avec un bouton, 
cf exemple:

```html
<form action="{{ path('nom_de_route_1') }}" method="POST">
    <input type="hidden" name="_method" value="DELETE"> <- /!\ faire bien attention a mettre input en hidden avec le value="delete"->
    <input type="submit" value="Delete">
</form>
```
On peut aussi passer par JS pour mettre un lien <a href> à la place:

```html
<a href="#" onclick="event.preventDefault(); document.getElementById('js_form_delete').submit();">Delete Artwork</a>
<a href="{{ path('nom_de_route_2') }}">Back to menu</a>

<form id="js_form_delete" action="{{ path('nom_de_route_1') }}" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
</form>
```
##### Validation des formulaires:
Dan le controller:
"use Symfony\Component\Validator\Constraints as Assert;"

Puis rajouter dans le fichier entity autant de fois qu'il y a des type de validation dans phpdoc au niveau des objet que l'on veut valider:
"@Assert\(type de validation ex: NotBlank)" puis entre () les options a voir dans les fichier vendor/.../contraints

Attention a bien desactier la validation html5(useless...) avec "formnovalidate"
Aller dans Vendor/Symfony/validator/constraints et vous y trouver des type de validation pour les forms.


##### Template de formulaire :
    On peut set les différente options du type de formulaire dans le fichier 
    /!\ Grace à la commande " symfony console make:form " on peut creer un template de formulaire qui sera stocké dans le fichier src/Form/(Variable)Type.php, On associe ce formulaire à une entité, il prend donc toute ses caractéristiques.
    On peut aussi set les différente options du formulaire dans la fonction builForm() " cela revient a modif createFormBuilder() "
    Enfin on appel ce formulaire grace a la fonction createForm() 
        /!\ Il est recommande de mettre le dataclass pour les formulaire

ainsi :

```php

$form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;
```
devient

```php
$form = $this->createForm(PinType::class);
```

    On peut dans le fichier src/form/(nomEntité)Type.php

    Modifier la méthode buildform() et lui rajouter des options...
    Par exemple pour rajouter des contraintes a des champs images du formulaire:
```php
public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('imageFile', VichImageType::class, [
            'label' => 'Image (PNG or JPG file)',
            'required' => false,
            'allow_delete' => true,
            'delete_label' => 'Delete?',
            'download_uri' => false,
            'constraints' => [                     //
                new Image(['maxSize' => '8M'])     //On instancie l'objet image qui contient cette contrainte maxSize...
            ]
        ])
        ->add('title')
        ->add('description')

       ;
    }``` 


### Check la validité d'un Token

Cf: Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


```php
/**
     * Checks the validity of a CSRF token.
     *
     * @param string      $id    The id used when generating the token
     * @param string|null $token The actual token sent with the request that should be validated
     */
    protected function isCsrfTokenValid(string $id, ?string $token): bool
    {
        if (!$this->container->has('security.csrf.token_manager')) {
            throw new \LogicException('CSRF protection is not enabled in your application. Enable it with the "csrf_protection" key in "config/packages/framework.yaml".');
        }

        return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
    }
```


### Requete http

Lors de l'injection de Request, on peut faire un $POST ou un $GET comme ceci:

```php

function (Request $request) {

    $request->request->all(); //On recupère toutes les données $POST sous forme d'un tableau

    $request->query->all();  //On recupère toutes les données $GET sous forme d'un tableau

    $request->request; // On a accès a plein de méthodes relatif à lobjet httpFoundation/InputBag
    $request->query; //idem sauf qu'il faut y avoir des param supplémentaire dans l'URL.

}
```
### Astuce et trucs

Vider le cache execute la cmd [symfony console cache:clear]

Lorsque que l'on voit un signe "~" collé devant le chemin d'un dossier cela faitr référence au dossier node_module.

Attention a l'ordre des routes car Symfonie les charge/cherche puis match ou non dans un certain ordre.
Pour pallier a certain problème de ce genre: on mettre des priorité aux routes. Par exemple priority=(int).

Pour mettre a jour les pack composer exécuter: "composer update"
Composer recipe voir toutes les recettes installées ajouter le nom du packages a la suite de la commande pour avoir des détails sur un package spécifique

Dans un fichier Twig: 
Le defaut| signifie si le button n'existe pas alors on utilise Create Pin
On met un filtre et on utilise une valeur par défaut.
```html
{{ SubmitButtonText|default('Create Pin') }} <- revient à faire->
{{ SubmitButtonText ?? 'Create Pin' }} 
```

### Astuce en cas de pépins

    Lorsque qu'une commande ne passe pas il peut être utile d'executer cette commande
    "symfony console cache:clear"


### Message Flash
    On peut creer des message flash:
Dans le fichier de vue twig:

```html
{% for type, messages in app.flashes %}
    {% for message in messages %}
        {{ message }}
    {% endfor %}
{% endfor %}
``` 
Dans le fichier controller

```php

$this->addFlash('type', 'message');

```


### Bootstrap et Symfony

    Pour integrer bootstrap dans symfony juste copier le link css et les script JS dans les block prevu a cet effet dans le base.html.twig

    On peut aussi check dans la doc les theme bootstrap prédéfinis pour Symfony
    par exemple: https://symfony.com/doc/current/form/form_themes.html

### Utilisation des Traits:

    cf: "https://weenesta.com/fr/blog/post/utiliser-traits-symfony"
        "https://openclassrooms.com/fr/courses/1665806-programmez-en-oriente-objet-en-php/1667373-les-traits"

    Un Trait en POO:

    cf:"https://www.php.net/manual/fr/language.oop5.traits.php"

    Dans nos projet il est bon de créer un dossier "src/Entity/Traits" Et d'y placer tous nos fichier traits.php
    On peut aussi creer dans ces fichiers un namespace : "namespace App\Entity\Traits"



### Webpack avec Webpack encore

    cf: "https://weenesta.com/fr/blog/post/symfony-webpack"

    -En production penser a minifier/compiler le code pour le rendre plus lisible: on peut s'aider de sass pour le css ou encore de webpack qui est un bundle afin d'avoir des fichiers css ou js etc.. dédiés.
    cf: "https://webpack.js.org/" 
    /!\ Attention! très long à configurer.. Du coup Symfony a créer un bundle: Webpack Encore (utilisable même avec d'autre projet que Symfony) qui facilite la generation du fichier de config Webpack et des config comme apr exemple l'accès a Sass/SCSS ou encore Typescript

{
-voir asset ou ?CDN? : dossier ou est géré le css/JS/img et qui est servi pour processer Webpack.
-check le bundle flashy-bundle
-php unit
-symfony flex et système de recette (recipe): 
cf "https://afsy.fr/avent/2017/08-symfony-flex-la-nouvelle-facon-de-developper-avec-symfony"
cf "https://github.com/symfony/recipes"
}

    Pour ce faire: installer le bundle webpack Encore avec composer, puis npm avec node.js
    Puis regarder dans les dossier créer comme /asset/ où se trouve les fichier css et js ainsi que les fichier img et autres....

    On se retrouve ainsi a travailler avec les dépendance JS et l'équivalent de composer pour php soit npm.

    Aller dans le fichier /webpack.config.js qui va configurer pour nous Webpack, on peut le modifier comme par exemple :
     ".addEntry('app', './assets/js/app.js')" où "app" est le nom du fichier cible se trouvant dans asset/js, on pourra le modifier, et aussi rajouter des entrées etc...

    Avec la commande npm run vous aurez la liste des commande que vous pouvez exécuter pour ce faire.
    Executer la commande npm run dev pour migrer les modifs faites dans assets dans un dossier qui sera créer : 
    /public/build/ ou se trouveront tous nos dossier css, js etc...
    Quand vous exécutez la cmd [npm run watch] pour automatiquement faire les compilations dès qu'un fichier sera modif.

    Pour compiler le projet et le mettre en production executer la cmd [npm run-script build]

    (ndlr: le fichier /package.json et /package-lock.json sont les équivalent de /composer.json et /...lock)

#### Importation des fichier CSS JS et Bootstrap avec Webpack

##### CSS/JS
    Pour charger les fichier JS et CSS il faut à la place de nos script et link tradionnels, appeler ces methodes du bundle webpack.
    Pour css: {{ encore_entry_link_tags('app')}} où "app" est le ficheir cible
    Pour JS:  {{ encore_entry_script_tags('app')}} où "app" est le fichier cible

##### Bootstrap importer via Sass
    Pour installer Bootstrap
    Executer la cmd [npm install bootstrap --save-dev] ou bien "npm install bootstrap -D"

    cf:"https://symfony.com/doc/current/frontend/encore/bootstrap.html"

    /!\Si on check bien les warning mess: il faut install jquery et popper.

    Donc executer la cmd [npm i jquery popper.js -D]
    Et le enabled dans le fichier assert/app.js



    /!\ Bien penser a modifier le chemin d'accès du link dans le fichier base.html.twig

        On modifiera toujours les fichiers dans /assets/ puis executer les commandes npm run dev/watch afin qu'il se mettent a jour dans public/build/

        Pour appeler une propriété Bootstrap dans le fichier scss il suffit de préfixer les propriété d'un
        "@extend " puis un ou plusieurs propriété séparées d'une virgule.

exemple:
```scss
    img {
        @extend .mb-2, .rounded, .shadow, .img-thumbnail
    }
``` 
autre exemple de syntaxe de fichier scss ou il y a une structure et notion d'héritage.
```scss
.vich-image{
    .form-check {
        @extend .mt-3;
    }

    img {
        @extend .mb-2;
        @extend .rounded;
        @extend .shadow;
        @extend .img-thumbnail;
    }
}
``` 

        A chaque fois que l'on modifie la configuration dans Webpack.config.js il faut couper et relancer nmp run watch
        si il ya une erreur ou un paquet manquant, suivre les instructions.



##### Utiliser Jquery et Sass/Scss et bootstrap

    Pour customiser le front utiliser ces deux langages dans le dossier /assert/
    Le css et bootstrap avec le dossier SCSS
    Je JS et Jquery avec le dossier JS



### Upload de fichier:

    Suivre les instructions de la doc.
    cf: "https://symfony.com/doc/current/controller/upload_file.html":

    Ou utiliser le bundle "VichUploaderBundle" 
    Installer le bundle avec la cmd [composer require vich/uploader-bundle]
    |->Comme c'est un bundle de contribution et non officiel il nous demande si l'on veut installer les recettes:
    "Yes"

    Suivre les étapes de configuration du dépot git
    cf: "https://github.com/dustin10/VichUploaderBundle/blob/master/docs/installation.md"


#### Pour resize une image 

    Dl le bundle en exécutant cette cmd [composer require liip/imagine-bundle]
    et voir cette page : "https://symfony.com/doc/master/bundles/LiipImagineBundle/installation.html#step-1-download-the-bundle"
    ainsi que le dépot github : "https://github.com/liip/LiipImagineBundle"


    /!\ use imagick


### Annotation phpdoc

    On peut ajouter des options en plus qui seront liées a des objets ou des méthodes en faisant ceci:

### Shell interactif Psysh
    Installation du bundle Psysh afin d'avoir un shell interactif dans la console et executer du code php par exemple.
    On peut le lancer ensuite avec la cmd [symfony console psysh] pour par exemple peupler sa BDD.

##### Peupler la BDD en ligne de commande avec Psysh
exemple:
```php
use App\Entity\User;
$user1 = new User;
$user1->setFirstName('John');

$em = $container->get('doctrine')->getManager(); //appeler EntityManager
$em->persist($user1);    //persister
$em->flush();            // Maj la BDD


$userRepo = $em->getRepository(User::class);    //pour recupere certaine entree
=> App\Repository\UserRepository {#2902}
$pinRepo = $em->getRepository(Pin::class);
=> App\Repository\PinRepository {#2922}

$p1 = $pinRepo->find(1);  //puis on fait appel au methode de la class en l'ocurrence recuperer le pin avec id 1 et on le met dans une variable $p1
$p1->getUser()->getFirstName(); // puis de meme pour recup les donnée on fait appel au getters sorti expected: "john"

//Puis persist et flush
``` 
##### Peupler la BDD avec de fausse donnée à l'aide de Fixtures

    cf google.....


### MAJ de composer etc....

    Connaitre sa version de Symfony cmd [symfony console --version]
    Pour faire une mise a jour automatique des dépendences bundle etc... exécuter la cmd [composer update]
    Pour voir la liste de ses recettes et les MAJ possibles executez la cmd [composer recipes]



### AUTHENTIFICATION/LOGIN SECURITE ETC....

    cf:"https://symfony.com/doc/current/security.html"

    Pour creer un entité User on peut directement taper la cmd [symfony console make:user] 
    On va creer un user avec tous les proprietés pour la sécu/authen.

    le fichier src/entity/User.php est crée
    le fichier src/Repository/UserRepository.php aussi
    On met a jour le fichier config/packages/security.yaml

    /!\Notez bien que la class User implemente UserInterface
    Lorsque l'on implemente une interface en POO cela revient a signer un contrat avec cette interface 
    On doit ainsi définir dans l'entité toutes les méthodes abstraites qui ont été défini au niveau de l'interface
    https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/oriente-objet-methode-classe-abstraite/

    cf: "##### Peupler la BDD avec Psysh"

#### Hashage d'un MDP:

    /!\ Ne jamais insérer un MDP directement dans la BDD il faut pour cela le hasher avant!!
    Puis on y ajoute un sel cad on concatene une chaine de caractere toujours de la même longueur, avant et/ou apres le hash.

    La commande du bundle security[symfony console security:encode-password] hash un mdp.

    Dans le fichier security.yaml on config le type d'encodage sur "auto" il prend ainsi le meilleur encodeur existant.
    Ou encore plusieurs encodeur que l'on configurera sur different type.







