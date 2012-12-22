WebCommandBundle
================
The aim of this bundle is to ease managing a Symfony-application on a shared-hosting environment. In a shared-hosting environment there's not always the possibility to execute console-commands. 
This bundle allows to execute a console-command from a Controller and gives feedback in a flahs-message.

The bundle is not intended to make you run long cron-tasks from the Controller but to provide tools to update your database-schema, or install assets.

Example:

```php

    /**
     * @Route("/", name="admin_index")
     * @Template("")
     */
    public function indexAction(\Symfony\Component\HttpFoundation\Request $request)
    {

        $form = $this->createForm('eriktrapman_commandselectortype');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $event = new \ErikTrapman\Bundle\WebCommandBundle\Event\CommandRunEvent(
                    $form->get('command')->getData(), 
                    $form->get('options')->getData());
                $this->get('event_dispatcher')->dispatch('eriktrapman.command.run', $event);
                return $this->redirect($this->generateUrl('admin_index'));
            }
        }
        return array('form' => $form->createView());
    }
```
