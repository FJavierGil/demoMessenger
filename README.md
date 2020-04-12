![logo UPM](https://raw.githubusercontent.com/laracabrera/AOS/master/tarea1/logo_upm.jpg) AOS: Ejemplo de servicios asíncronos
======================================

[![Minimum PHP Version](https://img.shields.io/badge/php-%5E7.4-blue.svg)](http://php.net/)
[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)
> Ejemplo de servicio asíncrono basado en mensajes _(con el gestor RabbitMQ)_

En este ejemplo se implementan dos servicios asíncronos que se comunican a través de un gestor de colas de mensajes.
El gestor de mensajes (_message broker_) empleado es [RabbitMQ][rabbitmq], que proporciona un sistema de negociación
de mensajes de código abierto. Por su parte, los servicios utilizan el protocolo [AMQP][amqp] para conectarse con 
el gestor de mensajes, y además ofrecen una interfaz para poder realizar dos sencillas operaciones:
- Enviar una notificación a la cola
- Consumir una notificación de la cola

En el ejemplo, cada notificación (en formato JSON) contiene un texto y una lista de direcciones de
correo electrónico similar a:
```json
{
  "textMessage": "Message text",
  "users": [
    "foo@example.com",
    "bar@example.com"
  ]
}
```
&nbsp;

![img](./public/img/imagen1.png)
### Servicio para el envío de notificaciones _(productor, puerto 8000)_
El servicio de envío de notificaciones actúa como **productor**: construye un mensaje que contiene la
notificación y lo entrega al intermediario, es decir, lo envía a la cola de mensajes. El mensaje permanecerá
en la cola en espera de ser consumido por algún servicio.

![img](./public/img/imagen2.png)

En el ejemplo la cola se ha denominado `messages_high`. Dentro de RabbittMQ, cuando un productor desea
encolar un nuevo mensaje, lo que hace es enviarlo a un intercambiador (_exchange_), que se
comporta como agente de enrutamiento. Este intercambiador (`high` en el ejemplo) es el responsable
del envío de los mensajes a las diferentes colas. 

### Servicio para consumir una notificación _(consumidor, puerto 8080)_
El servicio _consumir notificación_ actúa como **consumidor**: se conecta al gestor de mensajes, 
y, si hay algún mensaje, lo recoge. En este ejemplo, cuando se procesa un mensaje se simula el
envío de un texto a la lista de destinatarios de la notificación.

![img](./public/img/imagen3.png)

En esta implementación, tanto productor como consumidor utilizan un cuarto servicio
(denominado _php_fpm_) que proporciona el intérprete FPM (FastCGI Process Manager) de PHP.

***
## Despliegue de los servicios

Para acceder a estas operaciones se ha desarrollado una API que ha sido documentada
a través del esquema [OpenAPI 3.0][openapi]. Esta especificación se ha elaborado empleando el editor
[Swagger][swagger]. Adicionalmente se incluye la interfaz de usuario ([Swagger-UI][swagger-ui])
de esta fenomenal herramienta que permite realizar pruebas interactivas de manera completa y elegante.

Para desplegar el proyecto empleando Docker, basta con ejecutar los siguientes comandos 
desde el directorio raíz del proyecto:

```
> docker-compose up -d
> docker exec -u dev -it php_fpm bash
:/home/wwwroot$> cd ./aos
:/home/wwwroot/aos$> composer install
```

La ejecución de los tres últimos comandos sólo es necesaria la primera vez que se realiza el despliegue.
Desde la consola del sistema anfitrión se pueden observar los contenedores desplegados empleando:
```
> docker ps --format "{{.Names}}: Ports [{{.Ports}}]"
``` 

Una vez desplegado el proyecto, se podrá acceder a la interfaz de usuario de la especificación 
a través de [http://localhost:8000/api-docs/index.html][lh]. Además, también se podrá acceder a la herramienta
RabbitMQ Management en [http://localhost:15672/][rmq] (usuario _guest_ y password _guest_).

### Accediendo a la cola

La cola de mensajes se crea en el momento en que se recibe el primer mensaje. El
estado de las diferentes colas en el _broker_ puede observarse a través de la dirección
[http://localhost:15672/#/queues](http://localhost:15672/#/queues). Si se ha enviado algún
mensaje aparecerá una cola (denominada `messages_high`), y se pueden examinar todos los
detalles de la misma en tiempo real (la interfaz se actualiza automáticamente por defecto cada 5 segundos).

Aparte de utilizar la interfaz de la api, también es posible consumir los mensajes a través
de la consola de comandos (con mayor nivel de detalle). Para ello se deberán ejecutar los
siguientes comandos:
```
> docker exec -it -u dev php_fpm bash
:/home/wwwroot$> cd ./aos
:/home/wwwroot/aos$> bin/console -vvv --limit=1 messenger:consume
```

### Deteniendo los servicios

Como curiosidad, si se desea acceder a los detalles internos del funcionamiento de la aplicación
(implementada sobre el framework [Symfony][sf]) se puede acceder a la dirección
[http://localhost:8000/_profiler][profiler].

Finalmente, para detener la ejecución de los contenedores desde el anfitrión se ejecutará el comando:
```
> docker-compose stop
```

[lh]: http://localhost:8000/api-docs/index.html
[openapi]: https://www.openapis.org/
[profiler]: http://localhost:8000/_profiler
[swagger]: http://swagger.io/
[swagger-ui]: https://github.com/swagger-api/swagger-ui
[amqp]: https://www.amqp.org/
[rabbitmq]: https://www.rabbitmq.com/
[rmq]: http://localhost:15672/#/
[sf]: https://symfony.com/