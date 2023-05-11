<?php

namespace Drupal\first_module\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Request as Psr7Request;
use \Drupal\Core\Routing\TrustedRedirectResponse;

class ProductController extends ControllerBase
{
  protected $httpClientFactory;

  public function __construct(ClientFactory $httpClientFactory) {
    $this->httpClientFactory = $httpClientFactory;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client_factory')
    );
  }

  public function addProductToQuote($sku)
  {
    $productSku = $sku;
    $access_token = "0ddl5paofqsuq61hipacy69vyqgeaknz";
    $client = $this->httpClientFactory->fromOptions([
      'base_uri' => 'https://store-qa2.enphase.com/storefront/',
      'headers' => [
        'Authorization' => 'Bearer ' . $access_token,
        'Content-Type' => 'application/json'
      ],
    ]);

    $response = $client->get('/rest/V1/products/' . $productSku);
    $data = Json::decode($response->getBody());
    $payload = [
      "quote_id"=> "1187676",
      'id' => $data['id'],
      'name' => $data['name'],
      'sku' => $data['sku'],
      'price' => $data['price']
    ];
    $payloadJson = Json::encode($payload);
    $maskId=$this->getResponse();
//    // Create a new PSR-7 compatible request object.
//    $request = new Psr7Request(
//      'POST',
//      'https://store-qa2.enphase.com/storefront/rest/V1/guest-carts/'. $maskId,
//      [
//        'Authorization' => 'Bearer ' . $access_token,
//        'Content-Type' => 'application/json',
//      ],
//      $payloadJson
//    );
//
//    $response = $client->sendRequest($request);
//    $response = new TrustedRedirectResponse("https://store-qa2.enphase.com/storefront/en-us/checkout");
    $httpClient = \Drupal::service('http_client');
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $access_token,
    ];
    $response = $httpClient->request(Request::METHOD_POST, 'https://store-qa2.enphase.com/storefront/rest/V1/guest-carts/'. $maskId, [
      'headers' => $headers,
      'body' => $payloadJson,
    ]);
    if ($response->getStatusCode() == 200) {
      $responseData = json_decode($response->getBody()->getContents(), TRUE);
      return new JsonResponse($responseData);
    }
    else {
      return new JsonResponse("error");
    }

//    return  new Response($response);
  }

  public function getResponse() {
    $access_token = "0ddl5paofqsuq61hipacy69vyqgeaknz";
    $uri = 'https://store-qa2.enphase.com/storefront/rest/V1/guest-carts';
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $access_token,
    ];

    $httpClient = $this->httpClientFactory->fromOptions();
    $response = $httpClient->post($uri, [
      'headers' => $headers,
    ]);

    $content = $response->getBody()->getContents();
    $responseData = json_decode($content, TRUE);

    return new JsonResponse($responseData);
  }


}
