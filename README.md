# Vending Machine

## Problem definition
The goal of this program is to model a vending machine and the state it must maintain during its operation. How exactly the actions on the machine are driven is left intentionally vague and is up to the candidate

The machine works like all vending machines: it takes money then gives you items. The vending machine accepts money in the form of 0.05, 0.10, 0.25 and 1

You must have at least have 3 primary items that cost 0.65, 1.00, and 1.50. Also user may hit the button “return coin” to get back the money they’ve entered so far, If you put more money in than the item price, you get the item and change back.

## Specification

### Valid set of actions on the vending machine are:

* 0.05, 0.10, 0.25, 1 - insert money
* Return Coin - returns all inserted money
* GET Water, GET Juice, GET Soda - select item (Water = 0.65, Juice = 1.00, Soda = 1.50)
* SERVICE - a service person opens the machine and set the available change and how many items we have.

### Valid set of responses on the vending machine are:

* 0.05, 0.10, 0.25 - return coin
* Water,  Juice, Soda - vend item

### Vending machine must track the following state:

* Available items - each item has a count, a price and selector
* Available change - Number os coins available
* Currently inserted money

## Examples 
```
Example 1: Buy Soda with exact change
1, 0.25, 0.25, GET-SODA
-> SODA

Example 2: Start adding money, but user ask for return coin
0.10, 0.10, RETURN-COIN
-> 0.10, 0.10

Example 3: Buy Water without exact change
1, GET-WATER
-> WATER, 0.25, 0.10
```


# A solution

## Base environment

The base for the solution is the [Codium's PHP and PHPUnit Kata Bootstrap](https://github.com/CodiumTeam/php-kata-bootstrap), and all its info can be found on the `ENV.md` file. 

## Approach

The method I took for this problem was the "kata" iteration one. I tried to have the problem solved in the most naive way ASAP, and try to iterate and improve readability and organization on each iteration.

This included having code coverage of the functionality so each change was done over a safe net.   

I used PHPUnit for this "naive" approach, as I see it as a swiss-knife of the testing in php: you can use it for everything, the only important thing is the scope of your unit under test.

Following this quick-and-dirty modelling technique and not thinking with a specially DDD focused mind (only on some code organization) made me miss the point I think in one of the keys of the problem: identifying the vending machine as an Aggregate. Doing so, simplified a lot the way of dealing with the stocks of coins (live and coin hooper) and the item inventory: if they are part of the same aggregate you deal with all this information as a part of the machine and save it transactionally.    

Having seen that, all the "in-memory" specific coin/item repos/stores converted into parts of the aggregate in the domain. 

For the persistence of the machine itself I chose memcached as a quite simple key value service, which stores the full aggregate serialized. I used igbinary explicitly as memcached supports it but you need to compile with that param enabled, so this was easier to achieve.

I decided not to use a framework as base and also reduce the dependency usage to the minimum, not because I don't like symfony or dependencies, but just to try to do a simpler approach. About using symfony framework-bundle for CLI apps, I think it's a little bit "contaminated" with all the fw definitions and commmands. 

And like that, a lot of other things I'd have liked to implement but because of some issues at home (careless workers and problematic installations, including water leaks) I couldn't make it:
- Using a better App definition and using services from a specific kernel/container
- Testing full CLI functionality outside-in (behat)
- Rethink some unnecesary patterns (Response DTOs)
- Reconsider using some external dependencies (an Enum, DDD basics, symfony/console...)
- Rethink the way of dealing with the prices, as it's hardcoded on the app service
- And a lot of other small details :(  

## Usage

The entry point is on the `bin/app.php` file, and it has a little syntax explanation message.
