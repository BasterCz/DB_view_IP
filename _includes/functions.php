<?php
function Error500(string $location, string $locationName)
{

    $errorhtml = '<div class="col-md-12 text-center">';
    $errorhtml .= '<h1 >500</h1>';
    $errorhtml .= '<div class="mb-4 lead">Chybný formát vstupu.</div>';
    $errorhtml .= '<a href='. $location .' class="btn btn-link">Zpět na '. $locationName .'</a>';
    $errorhtml .= '</div>';
    echo $errorhtml;
}
function Error404(string $location, string $locationName)
{
    $errorhtml = '<div class="col-md-12 text-center">';
    $errorhtml .= '<h1 >404</h1>';
    $errorhtml .= '<div class="mb-4 lead">Výsledek nebyl nalezen v databázi.</div>';
    $errorhtml .= '<a href='. $location .' class="btn btn-link">Zpět na '. $locationName .'</a>';
    $errorhtml .= '</div>';
    echo $errorhtml;
}
function Title($tried, $success, $badgateway)
{
    if ($success) {
        return htmlspecialchars($tried);
    } else {
        if ($badgateway) {
            return "Error 500";
        } else {
            return "Error 404";
        }
    }
}
function tryToLogIn(&$e)
{
    if (!$e->isErrActive('mailEmpty')) {
        if ($e->isErrActive('mailSomehowWrong')) {
            $e->isActiveChange('mailWrong', true);
        } else {
            $e->isActiveChange('mailSomehowWrong', false);
            if ($e->isErrActive('passNo')) {
                $e->isActiveChange('passWrong', true);
            } else {
                $e->isActiveChange('passWrong', false);
            }
        }
    } else {
        $e->isActiveChange('mailWrong', true);
    }
}

function tryToChangePass(&$e)
{
    if ($e->isErrActive('newEmpty') || $e->isErrActive('samePass')) {
        $e->isActiveChange('newPassWrong', true);
    }
    else  {
        $e->isActiveChange('newPassWrong', false);
    }
}
