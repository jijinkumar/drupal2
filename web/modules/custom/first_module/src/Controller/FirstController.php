<?php

namespace Drupal\first_module\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Render\Element\Button;

class FirstController extends ControllerBase
{

  public function firstMethod()
  {
    $access_token = "9tubefmvmmbn0d58xjqbbu52c1g2v4rw";
    $client = Drupal::service('http_client_factory')->fromOptions([
      'base_uri' => 'https://store-qa2.enphase.com/storefront/',
      'headers' => [
        'Authorization' => 'Bearer ' . $access_token,
        'Content-Type' => 'application/json'],
      ]);
    $response = $client->get('/rest/V1/products/ENCHARGE-10-1P-NA-2', [
      'query' => [
        'per_page' => 10,
      ],
    ]);
    $data = json_decode($response->getBody());
    return array(
      '#theme' => 'my_template',
      '#items' => $data,
      '#title' => 'Product helo!'
    );
  }

  public function getProductDetails()
  {
    $database = \Drupal::database();
    $query = $database->select('commerce_product_variation_field_data', 'm')
             ->fields('m', ['sku', 'title', 'product_id'])
             ->orderBy('product_id', 'DESC')->execute();
    $data = $query->fetchAllAssoc('product_id');
    $details = [];
    foreach ($data as $productItems)
    {
      $access_token = "0ddl5paofqsuq61hipacy69vyqgeaknz";
      $client = Drupal::service('http_client_factory')->fromOptions(['base_uri' => 'https://store-qa2.enphase.com/storefront/',
        'headers' => [
          'Authorization' => 'Bearer ' . $access_token,
          'Content-Type' => 'application/json'],
        ]);
      $response = $client->get('/rest/V1/products/' . $productItems->sku);
      $data = json_decode($response->getBody());
      $details[] = $data;
    }
    return array(
        '#theme' => 'my_template',
        '#items' => $details,
        '#title' => 'Product Details'
      );
  }
}
