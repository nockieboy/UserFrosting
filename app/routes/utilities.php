<?php

    /**
     * Miscellaneous utility routes.
     *
     * @package UserFrosting
     * @author Alex Weissman
     */
     
    use UserFrosting as UF;
    
    global $app;
    
    // Generic confirmation dialog
    $app->get('/forms/confirm/?', function () use ($app) {
        $get = $app->request->get();
        
        // Load the request schema
        $requestSchema = new \Fortress\RequestSchema($app->config('schema.path') . "/forms/confirm-modal.json");
        
        // Get the alert message stream
        $ms = $app->alerts;         
        
        // Remove csrf_token
        unset($get['csrf_token']);
        
        // Set up Fortress to process the request
        $rf = new \Fortress\HTTPRequestFortress($ms, $requestSchema, $get);                    
    
        // Sanitize
        $rf->sanitize();
    
        // Validate, and halt on validation errors.
        if (!$rf->validate()) {
            $app->halt(400);
        }           
        
        $data = $rf->data();
        
        $app->render('components/common/confirm-modal.twig', $data);   
    }); 
    
    /**
     * Render the alert stream as a JSON object.
     *
     * The alert stream contains messages which have been generated by calls to `MessageStream::addMessage` and `MessageStream::addMessageTranslated`.
     * Request type: GET
     * @see \Fortress\MessageStream
     */
    $app->get('/alerts/?', function () use ($app) {
        $app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        if ($app->alerts){
            echo json_encode($app->alerts->getAndClearMessages());
        }
    });
    
    /**
     * Render a JS file containing client-side configuration data (paths, etc)
     *
     * Many client-sided Javascript functions need some basic information about how the site is configured.
     * Rather than hard-code it in Javascript, this automatically generates a JS array, called "site",
     * which contains this configuration information.
     * Request type: GET
     */
    $app->get($app->config('uri')['js_relative'] . '/config.js', function () use ($app) {
        $app->response->headers->set("Content-Type", "application/javascript");
        $app->response->setBody("var site = " . json_encode(
            [
                "uri" => [
                    "public" => $app->config('uri')['public']
                ],
                "debug" => $app->config('debug')
            ]
        ));
    });
    
    /**
     * Selects and renders the CSS for the current user's theme.
     *
     * Since user themes are configured dynamically, this provides a way for UF to automatically load the appropriate
     * CSS file for the current user's theme.
     * Request type: GET
     * @todo Support for minification
     */     
    $app->get($app->config('uri')['css_relative'] . '/theme.css', function () use ($app) {
        $app->response->headers->set("Content-Type", "text/css");
        $css_include = UF\APP_DIR . '/' . UF\TEMPLATE_DIR_NAME . '/' . $app->user->getTheme() . "/css/theme.css";
        $app->response->setBody(file_get_contents($css_include));
    });
    