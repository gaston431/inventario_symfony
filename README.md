# inventario_symfony

GitHub repository: https://github.com/gaston431/inventario_symfony.git

Develoment Tools:
	Operating System: Microsoft Windows 10
	XAMPP v3.3.0
		Apache/2.4.51 (Win64) OpenSSL/1.1.1l PHP/7.3.31 MariaDB 10.4.21
	Composer: dependency manager for php 2.1.12 
	PHP Framework: Symfony 5.4.2	

Urls vistas (punto 2):
	http://localhost:8000/								#Listado de articulos
	http://localhost:8000/articulos/{id}/movimientos	#Listado de movimientos de un articulo

API urls (punto 3): 	
	GET http://localhost:8000/api/articulos  					#Para consultar articulos  	
	GET http://localhost:8000/api/articulos/{id}/movimientos  	#Para consultar movimientos de un articulo	
	POST http://localhost:8000/api/articulo  					#Para crear articulos		
		Requiere pasar en el request body via Postman: numero,descripcion y ubicacion
	POST http://localhost:8000/api/movimiento  					#Para crear movimientos
		Requiere pasar en el request body via Postman: cantidad, tipo(compra, venta o recuento) y articulo_id