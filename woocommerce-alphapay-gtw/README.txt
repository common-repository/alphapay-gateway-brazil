Contributors: https://profiles.wordpress.org/lucasalphapay/
Donate link: https://www.alphapay.com.br/
Tags: alphapay, woocommerce, brazillian gateway
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds AlphaPay gateway to the WooCommerce plugin

== Description ==

### Add AlphaPay gateway to WooCommerce ###

This plugin adds AlphaPay gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Descrição em Português: ###

Adicione o plugin AlphaPay como método de pagamento em sua loja WooCommerce.

[AlphaPay - Payment Gateway] é um método de pagamento brasileiro desenvolvido pela LKS Onthology Desenvolvimento e Consultoria, em parceria comercial com da ITMAN e AlphaPay (https://alphapay.com.br/).

Este plugin possuem parcerias comerciais da ITMAN e da AlphaPay.
Este plugin foi desenvolvido a partir da documentação oficial da AlphaPay, (https://prd-sales.alphapay.com.br/swagger/index.html) e utiliza a última versão da API de pagamentos da AlphaPay.

Método de pagamento:

- **Transparente:** O cliente efetua o pagamento direto no seu site via woocommerce sem o abandono do carrinho.

= Compatibilidade =

Compatível com versões atuais do WooCommerce.

Este plugin também é compatível com o [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/), desta forma é possível enviar os campos de "CPF", "número do endereço" e "bairro".

= Instalação =

Confira o nosso guia de instalação e configuração do AlphaPay gateway to WooCommerce na aba [Installation](http://wordpress.org/plugins/woocommerce-alphapay/installation/).

= Integração =

Este plugin funciona perfeitamente em conjunto com:

* [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).
* [WooCommerce Multilingual](https://wordpress.org/plugins/woocommerce-multilingual/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando nosso canal:

* A nossa sessão de [FAQ](http://wordpress.org/plugins/woocommerce-alphapay/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-alphapay).

= Agradecimentos =

* [Jean Suzuke](http://itman.com.br/) contribuiu no desenvolvimento, integração e layout do Checkout Transparente.
* [Douglas Silva](http://alphapay.com.br/) contribuiu nos testes integrados, API e validações das capturas e processamentos.
* [Lucas Kague] (https://alphapay.com.br), (https://itman.com.br), contribuir nos testes integrados, validações e funcionalidades.

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose AlphaPay gateway to WooCommerce and fill in your profile, email, token and apikey.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Enviar os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ativar o plugin.

= Requerimentos: =

É obrigatório possuir uma conta na AlphaPay (http://gateway.alphapay.com.br/);
Versão do PHP >= 5.4+
Wordpress >= 4.x+
e ter instalado o [WooCommerce](http://wordpress.org/plugins/woocommerce/).

= Configurações na AlphaPay gateway to WooCommerce: =

<blockquote>Atenção: Não é necessário configurar qualquer URL em "Página de redirecionamento" ou "Notificação de transação", pois o plugin é capaz de comunicar o Gateway AlphaPay pela API quais URLs devem ser utilizadas para cada situação.</blockquote>

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Finalizar compra".

Habilite o Gateway AlphaPay para WooCommerce, adicione o seu e-mail e o token do AlphaPay. O token é utilizado para gerar os pagamentos e fazer o retorno de dados.

Você pode conseguir um token na AlphaPay em "Integrações" > "[Token de Segurança](https://gateway.alphapay.com.br/integracao/token-de-seguranca.jhtml)".

Eleger a opção de pagamento:

- **Checkout Transparente:** O cliente faz o pagamento direto em seu site na página de finalizar pedido utilizando a API do Gateway AlphaPay para WooCommerce.

Você ainda pode definir o comportamento da integração utilizando as opções:

- **Enviar apenas o total do pedido:** Permite enviar apenas o total do pedido no lugar da lista de itens, esta opção deve ser utilizada apenas quando o total do pedido no WooCommerce esta ficando diferente do total no Gateway AlphaPay para WooCommerce.
- **Prefixo de pedido:** Esta opção é útil quando você esta utilizando a mesma conta do Gateway AlphaPay para WooCommerce em várias lojas e com isso você pode diferenciar os pagamentos pelo prefixo.

= Checkout Transparente =

Para utilizar o checkout transparente é necessário utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

Com o **WooCommerce Extra Checkout Fields for Brazil** instalado e ativado você deve ir até "WooCommerce > Campos do Checkout" e configurar a opção "Exibir Tipo de Pessoa" como "Pessoa Física apenas".

Isto é necessário porque é obrigatório o envio de CPF para a AlphaPay, além de que a AlphaPay aceita apenas CPF.

Note que é necessário aprovação da AlphaPay para utilizar o Checkout Transparente, saiba mais em "[Como receber pagamentos pela AlphaPay](https://gateway.alphapay.com.br/receba-pagamentos.jhtml)".

Pronto, sua loja já pode receber pagamentos pelo AlphaPay gateway to WooCommerce.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce version 3.0 or latter installed and active.
* Only one account on [AlphaPay](http://gateway.alphapay.com.br/ "AlphaPay").

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado uma versão atual do plugin WooCommerce.
* Possuir uma conta na AlphaPay.
* Gerar um token de segurança na AlphaPay.


= AlphaPay recebe pagamentos de quais países? =

No momento a AlphaPay recebe pagamentos apenas do Brasil.

Configuramos o plugin para receber pagamentos apenas de usuários que selecionarem o Brasil nas informações de pagamento durante o checkout.

= Quais são os meios de pagamento que o plugin aceita? =

São aceitos todos os meios de pagamentos que a AlphaPay disponibiliza, entretanto você precisa ativa-los na sua conta.

Confira os [meios de pagamento e parcelamento](https://gateway.alphapay.com.br/para_voce/meios_de_pagamento_e_parcelamento.jhtml#rmcl).

= Como que plugin faz integração com Gateway AlphaPay para WooCommerce? =

A integração é orientada via a documentação oficial da AlphaPay que pode ser encontrada nos "[guias de integração](https://gateway.alphapay.com.br/receba-pagamentos.jhtml)" utilizando a última versão da API de pagamentos.

= Instalei o plugin, mas a opção de pagamento da AlphaPaysome durante o checkout. O que fiz de errado? =

Você esqueceu de selecionar o Brasil durante o cadastro no checkout.

A opção de pagamento pela AlphaPay funciona apenas com o Brasil.

= É possível enviar os dados de "Número", "Bairro" e "CPF" para a AlphaPay? =

Sim é possível, basta utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/).

= O pedido foi pago e ficou com o status de "processando" e não como "concluído", isto esta certo? =

Sim, esta certo e significa que o plugin esta trabalhando como deveria.

Todo gateway de pagamentos no WooCommerce deve mudar o status do pedido para "processando" no momento que é confirmado o pagamento e nunca deve ser alterado sozinho para "concluído", pois o pedido deve ir apenas para o status "concluído" após ele ter sido entregue.

Para produtos baixáveis a configuração padrão do WooCommerce é permitir o acesso apenas quando o pedido tem o status "concluído", entretanto nas configurações do WooCommerce na aba *Produtos* é possível ativar a opção **"Conceder acesso para download do produto após o pagamento"** e assim liberar o download quando o status do pedido esta como "processando".

= Ao tentar finalizar a compra aparece a mensagem "AlphaPay: Um erro ocorreu ao processar o seu pagamento, por favor, tente novamente ou entre em contato para obter ajuda." o que fazer? =

Esta mensagem geralmente aparece por causa que não foi configurado um **Token válido**.
Gere um novo Token na AlphaPay em "Preferências" > "[Integrações](https://gateway.alphapay.com.br/preferencias/integracoes.html)" e adicione ele nas configurações do plugin.

Outro erro comum é gerar um token e cadastrar nas configurações do plugin um e-mail que não é o proprietário do token, então tenha certeza que estes dados estão realmente corretos!

Se você tem certeza que o Token e Login estão corretos você deve acessar a página "WooCommerce > Status do Sistema" e verificar se **fsockopen** e **cURL** estão ativos. É necessário procurar ajuda do seu provedor de hospedagem caso você tenha o **fsockopen** e/ou o **cURL** desativados.

Para quem estiver utilizando o **Checkout Transparente** é obrigatório o uso do plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) para enviar o CPF ao AlphaPay, caso o contrário será impossível de finalizar o pedido, veja no [guia de instalação](http://wordpress.org/plugins/woocommerce-alphapay/installation/) como fazer isso.

Por último é possível ativar a opção de **Log de depuração** nas configurações do plugin e tentar novamente fechar um pedido (você deve tentar fechar um pedido para que o log será gerado e o erro gravado nele).
Com o log é possível saber exatamente o que esta dando de errado com a sua instalação.

Caso você não entenda o conteúdo do log não tem problema, você pode me abrir um [tópico no fórum do plugin](https://wordpress.org/support/plugin/woocommerce-alphapay#postform) com o link do log (utilize o [pastebin.com](http://pastebin.com) ou o [gist.github.com](http://gist.github.com) para salvar o conteúdo do log).

= O status do pedido não é alterado automaticamente? =

Sim, o status é alterado automaticamente usando a API de notificações de mudança de status da AlphaPay.

Caso os status dos seus pedidos não estiverem sendo alterados siga o tutorial da AlphaPay:

* [Não recebi o POST do retorno automático. O que devo fazer?](https://gateway.alphapay.com.br/atendimento/perguntas_frequentes/nao-recebi-o-post-com-retorno-automatico-o-que-devo-fazer.jhtml)

A seguir uma lista de ferramentas que podem estar bloqueando as notificações da AlphaPay:

* Site com CloudFlare, pois por padrão serão bloqueadas quaisquer comunicações de outros servidores com o seu. É possível resolver isso desbloqueando a lista de IPs da AlphaPay.
* Plugin de segurança como o "iThemes Security" com a opção para adicionar a lista do HackRepair.com no .htaccess do site. Acontece que o user-agent da AlphaPay esta no meio da lista e vai bloquear qualquer comunicação. Você pode remover isso da lista, basta encontrar onde bloquea o user-agent "jakarta" e deletar ou criar uma regra para aceitar os IPs da AlphaPay).
* `mod_security` habilitado, neste caso vai acontecer igual com o CloudFlare bloqueando qualquer comunicação de outros servidores com o seu. Como solução você pode desativar ou permitir os IPs da AlphaPay.

= Funciona com o checkout transparente da AlphaPay? =

Sim, funciona. Você deve ativar nas opções do plugin.
Note que é necessário aprovação da AlphaPay para utilizar o Checkout Transparente, saiba mais em "[Como receber pagamentos pela AlphaPay](https://gateway.alphapay.com.br/receba-pagamentos.jhtml)".

= O total do pedido no WooCommerce é diferente do enviado para a AlphaPay, como eu resolvo isso? =

Caso você tenha este problema, basta marcar ativar a opção **Enviar apenas o total do pedido** na página de configurações do plugin.

= Quais URLs eu devo usar para configurar "Notificação de transação" e "Página de redirecionamento"? =

Não é necessário configurar qualquer URL para "Notificação de transação" ou para "Página de redirecionamento", o plugin já diz para a AlphaPay quais URLs serão utilizadas.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Por favor, caso você tenha algum problema com o funcionamento do plugin, [abra um tópico no fórum do plugin](https://wordpress.org/support/plugin/woocommerce-alphapay#postform) com o link arquivo de log (ative ele nas opções do plugin e tente fazer uma compra, depois vá até WooCommerce > Status do Sistema, selecione o log do *alphapay* e copie os dados, depois crie um link usando o [pastebin.com](http://pastebin.com) ou o [gist.github.com](http://gist.github.com)), desta forma fica mais rápido para fazer o diagnóstico.

== Screenshots ==

1. Configurações do plugin.
2. Método de pagamento na página de finalizar o pedido.
3. Pagamento com cartão de crédito usando o Checkout Transparente.

== Changelog ==

= 2.14.0 - 2020/05/10 =