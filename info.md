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


## A faire

-php unit
-symfony flex et système de recette (recipe): 
cf "https://afsy.fr/avent/2017/08-symfony-flex-la-nouvelle-facon-de-developper-avec-symfony"
cf "https://github.com/symfony/recipes"

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

#### Dataclass :
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


### Check la validiter d'un Token

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

Pour mettre a jour les pack composer exécuter: "composer update"
Composer recipe voir toutes les recettes installées ajouter le nom du packages a la suite de la commande pour avoir des détails sur un package spécifique

Dans un fichier Twig: 
Le defaut| signifie si le button n'existe pas alors on utilise Create Pin
On met un filtre et on utilise une valeur par défaut.
```html
{{ SubmitButtonText|default('Create Pin') }} <- revient à faire->
{{ SubmitButtonText ?? 'Create Pin' }} 
``

### Astuce en cas de pépins

    Lorsque qu'une commande ne passe pas il peut être utile d'executer cette commande
    "symfony console cache:clear"