# tests
This is the code that you can clone in your Drupal root folder to get started with RedTest in a matter of minutes. It comes with a sample test for the Article content type which is installed by default when you use Drupal's "standard" profile.

## INSTALLATION

Go to your Drupal root folder and execute the following:

<pre><code>
git clone https://github.com/redcrackle/tests.git
cd tests
php composer.phar install
</code></pre>

We want to commit "tests" folder inside the project git and not as a separate git sub-module so remove .git folder from inside "tests" folder.

<pre><code>
rm -rf .git</code>
</pre>

## RUNNING TESTS

Go to Drupal root folder and execute the following:

<pre><code>
tests/bin/paratest --phpunit=tests/bin/phpunit --processes=4 --no-test-tokens --log-junit="tests/output.xml" --configuration=tests/phpunit.xml
</code></pre>

In above command, you can update the --processes argument to 1.5 times the number of cores present in your system.
