# Nubank-PHP

Biblioteca para acessar a API WEB do Nubank (Apenas leitura)

# Instalação

```
composer install charlesaugust44\nubankPHP
```

# Uso

## Login
Quando instanciar um novo objeto Nubank, ele tentará carregar o arquivo de sessão `nubank.json`, este arquivo contém o token de autorização das respostas do login e lift.
Se este arquivo nao existir ou o token estiver expirado, um novo login será necessário.

A propriedade `Nubank->status` pode ser usada para checar se um login é necessário.

```php
$nu = new Nubank();
 
if ($nu->status !== NubankStatus::AUTHORIZED) {
    $nu->login('cpf', 'password');
    $nu->printQRCodeSSID(); // para imprimir no CLI ou utilize getSSID para imprimir o seu próprio QRCode em outro lugar
    
    // Codigo para QRCode Lift aqui, se estiver imprimindo no CLI
}

// Aqui o status está como authorized

```
A API requer uma verificação dentro do aplicativo escaneando o QRCode, essa verificação pode ser feita acessando o menu na sua foto de perfil, então va em `Segurança`, depois em `Acesso no Navegador 

Essa verificação no app é obrigatória, lembre-se disso quando construir sua solução, não há maneiras de fazer um login automatico.

O metodo `lift()` pode ser usado para check se o QRCode foi lido, coloque-o em um loop com delay:

```php
$nu = new Nubank();
 
if ($nu->status !== NubankStatus::AUTHORIZED) {
    $nu->login('cpf', 'password');
    $nu->printQRCodeSSID(); // para imprimir no CLI ou utilize getSSID para imprimir o seu próprio QRCode em outro lugar
    
    for ($tryNumber = 0; $tryNumber < 15; $tryNumber++) {
      sleep(1);
   
      try {
          NubankTest::$nu->lift();
          break;
      } catch (ClientException $e) {
          if ($e->getCode() === 404) {
              // 404 significa que este SSID especifico não foi lido ainda.
          }
      }
   }
}

// Aqui o status está como authorized

```
Quando estiver esperando pela leitura 15 segundo deve ser o suficiente, qualquer coisa cima disso você deverá utilizar um novo SSID por questões de segurança.

## Fetch list of bills

A lista de faturas contém as futuras, a aberta e as passadas, nesta ordem repectivamente. Esta função retorna uma model `BillSummary`

```php
$bills = $nu->fetchBills();
```

## Fetch items of a bill

```php
$billsSummary = $nu->fetchBills();
$bill = $nu->fetchBillItems($billsSummary->bills[0]);
```
