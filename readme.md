# PooolClient

this is a PHP client for the project management application [POOOL](https://www.poool.cc/).

## Installation

Install via Composer

    composer require dieschittigs/poool-client

## Usage

Create an instance like this:

    use DieSchittigs\PooolClient;
    require 'vendor/autoload.php';

    $poool = new PooolClient('you@mail.com', 'yourpassword');

And use it like this:

    // Get latest entries from address book
    $addressBook = $poool->get('https://app.poool.cc/api/1/address_book?search_company=true&search_company_subsidiary=true&search_person=true&search_term=');

    // Search for projects
    $poool->post('https://app.poool.cc/api/1/project/management/search', [
        "search"=>[
            "filterGroups"=>[
                [
                    [
                        "option_id"=>"orderState",
                        "operator"=>"=",
                        "value"=>1
                    ],
                    [
                        "option_id"=>"ticketState",
                        "operator"=>"=",
                        "value"=>"open"
                    ]
                ]
            ],
            "fullText"=>""
        ]
    ]);

Poool has no official API documentation, so additional routes must be extracted by observing the calls
made within app.poool.cc.

©ISC [Die Schittigs](https://www.dieschittigs.de)
