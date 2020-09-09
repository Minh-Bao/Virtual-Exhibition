### description d'un Pin

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

A voir redirectToRoute();
Alias Symfony console sc dans le terminal
Customiser les pages d'erreur(cf: symfony.com)
Check page https://www.doctrine-project.org/ pour se mettre a jour sur Doctrine.

Voir EntityManagerInterface....

    Pour persist et flush on a besoin de l'EntityManagerInterface en faisant injection de dépendance(*). function(EntityManagerInterface $em)

    Ce la revient a faire : $em = $this->getDoctrine()->getManager();
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
