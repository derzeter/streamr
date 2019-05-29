streamr PHP post library 
========================

Introduction
------------
https://www.streamr.com is a project where you can stream data for crypto currency (DATA)
This implementation is a PHP implementation which permits you to POST to your app, a data stream.

The php library works fine on PHP 7.x (dependency on PHP curl library)

 This example works out of the box and permits you to POST to a test stream.

Actual configuration :

 - Stream ID : JzYcmMY6RcSCkk3x6Aglrw
 - API Key (write only) : EwOgLyt2R7ST1n67g7QjFgBMed9w_lT62T4DMTZHSnCA
 
Installation
------------

1. Clone the git repository or download it.
2. Modify post2steramr.php changing api_key and stream_id by yours
3. Use it ! 

Usage
-----
You can run from command line : php post2streamr.php 


TODO
----

Some easy things left to do :

- errors handling for GetToken method
- errors handling for Postdata method

Some harder things left to do :

- websocket method to post data
- read data from a stream

The End
-------

Enjoy. derzeter
Started in 2019



