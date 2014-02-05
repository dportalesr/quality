-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Servidor: 10.33.143.26
-- Tiempo de generación: 02-02-2014 a las 23:26:36
-- Versión del servidor: 5.0.95-log
-- Versión de PHP: 5.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `mudanz13_site`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `about`
--

CREATE TABLE IF NOT EXISTS `about` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `intro` text,
  `src` varchar(255) default '',
  `descripcion` text,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `about`
--

INSERT INTO `about` (`id`, `intro`, `src`, `descripcion`, `created`) VALUES
(1, '<h2>Transportes y Mudanzas Quality&nbsp;</h2>\r\n<p>Ofrece los servicios de transporte de carga y mudanzas especializadas, satisfaciendo as&iacute; las necesidades de nuestros clientes brind&aacute;ndoles un servicio de alta calidad, r&aacute;pido y al mejor precio.</p>', 'upload/imagen_213873861371.jpg', '<div>Misi&oacute;n: Transportes y Mudanzas Quality&nbsp;es una empresa seria y comprometida, que ofrece los servicios de transporte de carga y mudanzas especializadas, satisfaciendo as&iacute; las necesidades de nuestros clientes brind&aacute;ndoles un servicio de alta calidad, r&aacute;pido y al mejor precio.</div>\r\n<div><br />Visi&oacute;n: Transportes y Mudanzas Quality tiene como visi&oacute;n ser una empresa l&iacute;der en el ramo de Transportes a nivel nacional, mediante el uso de equipo moderno e infraestructura de alta calidad que nos posicione en el mercado como una empresa altamente competitiva, responsable, segura y honesta en el &aacute;rea de Transportes y Mudanzas de M&eacute;xico.</div>', '2013-09-10 00:52:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  `src` varchar(255) NOT NULL,
  `enlace` varchar(255) default NULL,
  `activo` tinyint(1) default '1',
  `caducidad` date default NULL,
  `orden` int(10) unsigned default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `banners`
--

INSERT INTO `banners` (`id`, `nombre`, `src`, `enlace`, `activo`, `caducidad`, `orden`, `created`) VALUES
(1, 'Banner 1', 'upload/safe.png', 'http://quality.pulsem.mx/servicios', 1, NULL, 1, '2013-09-11 12:44:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carousels`
--

CREATE TABLE IF NOT EXISTS `carousels` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `src` varchar(255) default '',
  `enlace` varchar(255) default NULL,
  `descripcion` text,
  `activo` tinyint(1) default '1',
  `orden` int(10) unsigned default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `carousels`
--

INSERT INTO `carousels` (`id`, `src`, `enlace`, `descripcion`, `activo`, `orden`, `created`) VALUES
(14, 'upload/imagen_2.jpg', '', '', 1, 2, '2013-12-18 10:53:51'),
(15, 'upload/imagen_213873860391.jpg', '', '', 1, 1, '2013-12-18 11:00:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned default NULL,
  `autor` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `web` varchar(255) default NULL,
  `descripcion` text,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `comments`
--

INSERT INTO `comments` (`id`, `parent`, `parent_id`, `autor`, `email`, `web`, `descripcion`, `created`) VALUES
(2, 'Post', 1, 'prueba', 'melissa@pulsem.mx', NULL, 'Buena Informaci&oacute;n.', '2013-09-11 18:38:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postimgs`
--

CREATE TABLE IF NOT EXISTS `postimgs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned default NULL,
  `src` varchar(255) NOT NULL,
  `portada` tinyint(1) default '0',
  `descripcion` text,
  `orden` int(10) unsigned default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `postimgs`
--

INSERT INTO `postimgs` (`id`, `post_id`, `src`, `portada`, `descripcion`, `orden`, `created`) VALUES
(1, 1, 'upload/organizada2.jpg', 1, '', 1, '2013-09-11 13:57:46'),
(2, 1, 'upload/organizada3.jpg', 0, NULL, 2, '2013-09-11 13:58:26'),
(5, 2, 'upload/think.jpg', 1, NULL, 3, '2014-01-02 16:48:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) default '1',
  `comment_count` int(11) default '0',
  `postimg_count` int(11) default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id`, `nombre`, `slug`, `descripcion`, `activo`, `comment_count`, `postimg_count`, `created`) VALUES
(1, 'Pasos para una mudanza organizada', '1_pasos-para-una-mudanza-organizada', '<p><span>Al menos dos meses antes de la mudanza</span></p>\r\n<ul>\r\n<li>Si decides contratar servicios profesionales busca informaci&oacute;n con suficiente antelaci&oacute;n.</li>\r\n<li>Selecciona varias empresas de mudanza y pide que visiten su domicilio para efectuar con rigor un presupuesto, el cual deber&aacute; ser presentado por escrito (no solicites ni aceptes presupuestos por tel&eacute;fono).</li>\r\n<li>Elabora un archivo para organizar todo lo relacionado con el proceso (fechas, presupuestos, facturas, folletos, etc&eacute;tera).</li>\r\n<li>Aprovecha la mudanza para ordenar tus pertenencias. Separa aquellos art&iacute;culos que ya no quieras conservar y desees donar o desechar.</li>\r\n<li>Notifica tu cambio de domicilio a las personas e instituciones que deban estar enteradas (Bancos, compa&ntilde;&iacute;a de seguros, servicios, publicaciones, etc&eacute;tera).</li>\r\n<li>Si posees antig&uuml;edades u obras de arte, averigua si requieren un seguro especial para la mudanza.</li>\r\n</ul>\r\n<p><span><br /><span class="subtitulo">Un mes antes de la mudanza</span><br /></span></p>\r\n<ul>\r\n<li>Lleva un inventario de los art&iacute;culos que se van a transportar, identificando las cajas que ser&aacute;n utilizadas y su contenido. Aux&iacute;liate con las hojas de contenido que aqu&iacute; te proporcionamos.</li>\r\n<li>Adquiere las cajas y materiales de empaque que necesitar&aacute;s para la mudanza. Ver lista de art&iacute;culos sugeridos.</li>\r\n<li>Designa un &aacute;rea espec&iacute;fica de la casa, que no bloquee el paso, para colocar y almacenar las cajas y art&iacute;culos empacados.</li>\r\n<li>Elige el tipo de cobertura que requerir&aacute;s para asegurar los art&iacute;culos durante el trayecto de la mudanza.</li>\r\n<li>Confirma tu reservaci&oacute;n con la compa&ntilde;&iacute;a de mudanza.</li>\r\n<li>Comienza por empacar los art&iacute;culos que no sean indispensables durante los pr&oacute;ximos d&iacute;as. Ver tips para empacar.</li>\r\n</ul>\r\n<p><span class="subtitulo">Dos semanas antes de la mudanza</span></p>\r\n<ul>\r\n<li>Contin&uacute;a empacando.</li>\r\n<li>Considera vender (venta de garaje), regalar o donar aquellos art&iacute;culos y objetos en buen estado que ya no vayas conservar.</li>\r\n<li>Los art&iacute;culos delicados, obras de arte y antig&uuml;edades pueden ser empacados por los especialistas de las franquicias Todo de Cart&oacute;n.</li>\r\n<li>Guarda documentos personales importantes en un lugar seguro y accesible.</li>\r\n<li>Regresa a sus due&ntilde;os las cosas prestadas que ya no necesitas, y aprovecha para recuperar los art&iacute;culos que has prestado a amigos, vecinos y familiares.</li>\r\n</ul>\r\n<p><span>Una semana antes de la mudanza</span></p>\r\n<ul>\r\n<li>Contin&uacute;a empacando.</li>\r\n<li>En caso de haber contratado a una compa&ntilde;&iacute;a profesional, coloca en un espacio aparte aquellas cajas y art&iacute;culos que transportar&aacute;s por tu cuenta, as&iacute; evitar&aacute;s confusiones.</li>\r\n<li>Coloca por separado, en una caja, los art&iacute;culos que ser&aacute;n usados de inmediato una vez hecha la mudanza (papel de ba&ntilde;o, toallas, etc&eacute;tera).</li>\r\n<li>Prepara una bolsa con objetos personales que necesitar&aacute;s durante la mudanza (ropa limpia, art&iacute;culos de tocador, medicinas, planos, comida y bebidas, etc.). Separa y marca de manera que te resulte f&aacute;cil encontrarla cuando la necesites.</li>\r\n<li>Desconecta aquellos aparatos que ya est&eacute;n listos para vaciarse, descongelarse o guardarse.</li>\r\n</ul>\r\n<p><span>El d&iacute;a de la mudanza</span></p>\r\n<ul>\r\n<li>Si es posible, fotograf&iacute;a los muebles valiosos antes de realizar la mudanza. De esta manera, en caso de sufrir alg&uacute;n desperfecto facilitar&aacute;s su reclamaci&oacute;n.</li>\r\n<li>Nunca env&iacute;es en la mudanza objetos de valor como joyas, p&oacute;lizas de seguros, documentos legales, etc. Ll&eacute;valos t&uacute; mismo.</li>\r\n<li><span style="line-height: 1.33;">Termina el inventario de todos los enseres, cajas y contenidos que se van a transportar. Esta misma operaci&oacute;n tambi&eacute;n la deber&aacute; realizar la compa&ntilde;&iacute;a de mudanzas.</span></li>\r\n<li><span style="line-height: 1.33;">Antes de que la mudanza llegue al nuevo domicilio, es conveniente cerciorarse de que las escaleras, ascensores y pasillos est&eacute;n despejados. Para facilitar el descenso de cajas y muebles.</span></li>\r\n<li><span style="line-height: 1.33;">Tambi&eacute;n es importante coordinar a la gente en ambos domicilios para cerciorarse de lo que cargan y descargan en su momento.</span></li>\r\n<li><span style="line-height: 1.33;">Es recomendable estar presente durante la mudanza, para poder contestar a cualquier pregunta de los operarios y ayudarles. Lo ideal es que tres personas supervisen las tareas: una en la antigua vivienda, otra en la zona de transporte y otra en la nueva casa. Si alguien permanece junto a los operarios hasta el fin de la mudanza, aconsejamos echar una mirada final al inmueble, para comprobar que no se haya olvidado nada.</span></li>\r\n<li><span style="line-height: 1.33;">Revisa tus posesiones una vez que hayan sido descargadas. Cerci&oacute;rate que lleguen el mismo n&uacute;mero de cajas y mobiliario, y naturalmente en buen estado.</span></li>\r\n</ul>\r\n<p><span>&nbsp;</span></p>\r\n<p><span>&nbsp;</span></p>\r\n<p><span>&nbsp;</span></p>', 1, 1, 2, '2013-09-11 13:57:46'),
(2, 'Un mes antes de la Mudanza', '2_un-mes-antes-de-la-mudanza', '<p><span class="subtitulo">Un mes antes de la mudanza</span></p>\r\n<ul>\r\n<li>Lleva un inventario de los art&iacute;culos que se van a transportar, identificando las cajas que ser&aacute;n utilizadas y su contenido. Aux&iacute;liate con las hojas de contenido que aqu&iacute; te proporcionamos.</li>\r\n<li>Adquiere las cajas y materiales de empaque que necesitar&aacute;s para la mudanza. Ver lista de art&iacute;culos sugeridos.</li>\r\n<li>Designa un &aacute;rea espec&iacute;fica de la casa, que no bloquee el paso, para colocar y almacenar las cajas y art&iacute;culos empacados.</li>\r\n<li>Elige el tipo de cobertura que requerir&aacute;s para asegurar los art&iacute;culos durante el trayecto de la mudanza.</li>\r\n<li>Confirma tu reservaci&oacute;n con la compa&ntilde;&iacute;a de mudanza.</li>\r\n<li>Comienza por empacar los art&iacute;culos que no sean indispensables durante los pr&oacute;ximos d&iacute;as.&nbsp;</li>\r\n</ul>', 1, 0, 1, '2013-09-12 10:23:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `src` varchar(255) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) default '1',
  `orden` int(10) unsigned default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `nombre`, `slug`, `src`, `descripcion`, `activo`, `orden`, `created`) VALUES
(1, 'Servicio Local y Nacional', '1_servicio-local-y-nacional', 'upload/2.jpg', '<p><span style="line-height: 1.33;">Brindamos servicio a lo largo y ancho de toda la Rep&uacute;blica Mexicana. Vamos hasta donde t&uacute; necesites y llevamos lo que tu casa o negocio requiera. Contamos con toda la experiencia, personal y camiones para moverte por todo el pa&iacute;s.</span></p>\r\n<p>&iexcl;Nos vemos en tu pr&oacute;ximo destino!&nbsp;</p>\r\n<p>&iexcl; Contactanos !</p>', 1, 1, '2013-09-11 14:03:28'),
(2, 'Exclusivo o Compartido', '2_exclusivo-o-compartido', 'upload/9.jpg', '<p><span style="line-height: 1.33;">Contamos con servicio de transporte exclusivo y compartido. En el transporte exclusivo reservamos un cami&oacute;n completamente para tus pertenencias y todo lo que quieras meter dentro de &eacute;l, ideal para mudanzas o transportes urgentes o que requieran mucho espacio. En el servicio compartido tus pertenencias viajan en conjunto con la de otros clientes y el costo del servicio es compartido, esto reduce el costo del envi&oacute;. Puedes contratar desde unos cuantos m</span><sup>2</sup><span style="line-height: 1.33;">&nbsp;hasta un cuarto, medio cami&oacute;n o m&aacute;s.</span></p>', 1, 2, '2013-09-11 14:04:45'),
(3, 'Repartos y Recolecciones', '3_repartos-y-recolecciones', 'upload/4.jpg', '<p><span style="line-height: 1.33;">Realizamos repartos y recolecciones para diferentes tipos de empresas y productos, ya sea dentro de la ciudad o en diferentes estados de la Rep&uacute;blica. Nuestro personal altamente capacitado y nuestros camiones satisfacen todas las necesidades de tu empresa o negocio.</span></p>', 1, 3, '2013-09-11 14:05:43'),
(4, 'Giras art&iacute;sticas', '4_giras-artisticas', 'upload/6.jpg', '<p><span style="line-height: 1.33;">En nuestros camiones puedes transportar: bocinas, tarimas, escenarios, equipo de sonido, instrumentos etc. Todo para tus conciertos y giras art&iacute;sticas. Hemos trabajado para varios eventos musicales que se organizan en la Pen&iacute;nsula. </span></p>\r\n<p><span style="line-height: 1.33;">&iexcl;Experiencia y Calidad comprobada!</span></p>', 1, 4, '2013-09-11 14:06:29'),
(5, 'Servicio de empaque y almacenaje', '5_servicio-de-empaque-y-almacenaje', 'upload/1.jpg', '<p><span style="line-height: 1.33;">Despreoc&uacute;pate por el empaque de tus cosas y contrata nuestro Servicio Especializado de Empaque.&nbsp;</span><span style="line-height: 1.33;">Nosotros nos encargamos de guardar tus cosas y darles la debida protecci&oacute;n. Contamos con una amplia bodega para resguardar tus pertenencias.</span></p>', 1, 5, '2013-09-11 14:07:07'),
(6, 'Transporte de autos y motos', '6_transporte-de-autos-y-motos', 'upload/mudanza_carro.jpg', '<p><span>Nos especializamos en transportar cualquier autom&oacute;vil as&iacute; como motocicletas.</span></p>\r\n<p><span style="line-height: 1.33;">De esta forma no tendr&aacute;s que ir manejando hasta tu nuevo destino, </span></p>\r\n<p><span style="line-height: 1.33;">nosotros &nbsp;lo enviamos de forma &nbsp;segura y r&aacute;pida.</span></p>', 1, 6, '2013-09-11 14:07:51'),
(7, 'Seguro de carga', '7_seguro-de-carga', 'upload/5.jpg', '<p><span style="line-height: 1.33;">El seguro de carga protege tu mercanc&iacute;a ante cualquier eventualidad durante el transporte. Una protecci&oacute;n extra para tu mudanza har&aacute; que viaje de forma m&aacute;s segura. El seguro responde por cualquier da&ntilde;o, percance o robo que pueda sufrir la unidad durante el trayecto.</span></p>\r\n<p>&nbsp;</p>', 1, 7, '2013-09-11 14:08:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(255) default NULL,
  `apellidos` varchar(255) default NULL,
  `master` tinyint(1) default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nombre`, `apellidos`, `master`, `created`) VALUES
(1, 'pulsem', '327d3429df2c4512edc07ed9e948aa75f5d14f50', 'Master', NULL, 1, '2010-01-01 00:00:00'),
(2, 'admin.qual', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Josu&eacute;', 'Mendez', 1, '2010-01-01 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
         