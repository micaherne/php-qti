Stuff to do just to get it working soundly:

Create new "not implemented" generator functions in FunctionGenerator for all relevant elements. There are various new elements like "min", "max", "mathOperator" etc. which are just being picked up by the identity transform but should be throwing an exception. It might be worth considering holding an explicit list of elements which are passed to the identity transform, rather than defaulting to it. [DONE]

The value attribute on Variable is potentially flaky. It's generally down to calling code to make sure it's an array or a single value depending on the cardinality of the variable. I'd rather have this done with an accessor, although it would probably mean fixing hundreds of instances throughout the code.

Go through each implemented element in the spec and make sure the class / closure generator implements it fully. Add unit tests for each. Create a list of full/partial/non implementation of each.

Get the existing interactions working soundly and implement as many of the unimplemented as possible.

Improve the item controller. Implement the full item lifecycle as methods, and take the state into account in methods like displayItemBody etc. Take notice of the adaptive attribute. The demo may also need to be improved to allow the user to control the state better.

