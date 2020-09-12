### Description d'un Pin

    -Title
    -Description
    -Image
    -Auteur
    -Comment (?)

#### Description de la table pins

    -id
    -title
    -description
    -createdAt
    -updatedAt


## A faire/ A voir

-nvm est un outil pour installer et utiliser a l'envie les différentes version de node.js

-déployer gratuitement son appli avec Heroku: 
cf:"https://www.heroku.com/"

-installer npm ou yard cf: node.js

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

```html.twig
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

    Pour integrer boot strap dans symfony juste copier le link css et les script JS dans les block prevu a cet effet dans le base.html.twig

    On peut aussi check dans la doc les theme bootstrap prédéfinis pour Symfony
    par exemple: https://symfony.com/doc/current/form/form_themes.html



### Webpack

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

    (ndlr: le fichier /package.json et /package-lock.json sont les équivalent de /composer.json et /...lock)

#### Importation des fichier CSS JS et Bootstrap avec Webpack

##### CSS/JS
    Pour charger les fichier JS et CSS il faut à la place de nos script et link tradionnels, appeler ces methodes du bundle webpack.
    Pour css: {{ encore_entry_link_tags('app')}} où "app" est le ficheir cible
    Pour JS:  {{ encore_entry_script_tags('app')}} où "app" est le fichier cible

##### Bootstrap
    Pour installer Bootstrap
    Executer la cmd [npm install bootstrap --save-dev] ou bien "npm install bootstrap -D"

    cf:"https://symfony.com/doc/current/frontend/encore/bootstrap.html"

    /!\Si on check bien les warning mess: il faut install jquery et popper.

    Donc executer la cmd [npm i jquery popper.js -D]
    Et le enabled dans le fichier assert/app.js



    /!\ Bien penser a modifier le chemin d'accès du link dans le fichier base.html.twig

        On modifiera toujours les fichiers dans /assets/ puis executer les commandes npm run dev/watch

        A chaque fois que l'on modifie la configuration dans Webpack.config.js il faut couper et relancer nmp run watch
        si il ya une erreur ou un paquet manquant, suivre les instructions.





