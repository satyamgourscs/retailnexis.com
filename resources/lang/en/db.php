<?php

/*
|--------------------------------------------------------------------------
| Fallback strings for keys also stored in the landlord `translations` table.
|--------------------------------------------------------------------------
|
| Error views (and early requests) may run before DB translations are loaded.
| These defaults ensure __(`db.*`) never shows raw keys.
|
*/

return [
    'Home' => 'Home',
    'Go Back' => 'Go Back',
    'Oh snap! We are lost' => 'Oh snap! We are lost…',
    'It seems we can not find what you are looking for Perhaps searching can help or go back to' => 'It seems we cannot find what you are looking for. Try searching or go back to',
    "We'll be back soon!" => "We'll be back soon!",
    "Sorry for the inconvenience. We're performing some working to improve your experience and will be back online shortly!" => "Sorry for the inconvenience. We're working to improve your experience and will be back online shortly!",
    'Oh server just snapped!' => 'Oh — the server hit a problem.',
    'An error occured due to server not being to able to handle your request' => 'An error occurred; the server could not handle your request.',
    'Sorry this page is dead!' => 'Sorry — this page is no longer available.',
    'The page is expired due to session expiration Just refresh the page or hit the button below' => 'This page has expired (session). Refresh the page or use the button below.',
];
