<?php
require_once 'vendor/autoload.php';

$shop = '';
$accessToken = '';
// Hàm để thực hiện yêu cầu cURL
function executeGraphQL($shop, $accessToken, $query, $variables = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://$shop.myshopify.com/admin/api/2025-01/graphql.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "X-Shopify-Access-Token: $accessToken"
    ]);
    $data = ['query' => $query];
    if ($variables  ) {
        $data['variables'] = $variables;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($response, true);
}


// Hàm để lấy danh sách sản phẩm
function fetchProducts($shop, $accessToken)
{
    $query = <<<'GRAPHQL'
{
  products(first: 10) {
    edges {
      node {
        id
        title
        handle
        vendor
        productType
        tags
      }
    }
    pageInfo {
      hasNextPage
    }
  }
}
GRAPHQL;
    return executeGraphQL($shop, $accessToken, $query);


//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, "https://$shop.myshopify.com/admin/api/2025-01/graphql.json");
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_POST, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, [
//        'Content-Type: application/json',
//        "X-Shopify-Access-Token: $accessToken"
//    ]);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));
//
//    $response = curl_exec($ch);
//    curl_close($ch);
//
//    return json_decode($response, true);
}

function createProduct($shop, $accessToken, $formData) {
    $mutation = <<<'GRAPHQL'
    mutation($input: ProductInput!) {
      productCreate(input: $input) {
        product {
          id
          title
          handle
        }
        userErrors {
          field
          message
        }
      }
    }
    GRAPHQL;

    return executeGraphQL($shop, $accessToken, $mutation, [
        'input' => [
            'title' => $formData['title'],
            'handle' => $formData['handle'],
            'vendor' => $formData['vendor'],
        ]
    ]);
}
createProduct($shop, $accessToken, [
        'title' => "phone 02",
        'handle' => "phone 02",
        'vendor' => "son ha 02"
]);
$productsData = fetchProducts($shop, $accessToken);
header('Content-Type: application/json'); print_r($productsData);
?>
