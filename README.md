# tests
This is the code that you can clone in your Drupal root folder to get started with RedTest in a matter of minutes. It comes with a sample test for the Article content type which is installed by default when you use Drupal's "standard" profile.

## INSTALLATION

Go to your Drupal root folder and execute the following:

<pre><code>
git clone https://github.com/redcrackle/tests.git
cd tests
php composer.phar install
</code></pre>

## RUN THE TESTS

Go to Drupal root folder and execute the following:

<pre><code>
tests/bin/paratest --phpunit=tests/bin/phpunit --processes=4 --no-test-tokens --log-junit="tests/output.xml" --configuration=tests/phpunit.xml
</code></pre>
