<section class="antialiased p-6 rounded-lg max-w-3xl">
    <form id="paymentForm">
        <div class="">
            <div class="min-w-0 flex justify-between gap-6 space-y-8">
                <div class="space-y-4 mb-4 w-full">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Checkout Details</h2>
                    <hr>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Your name </label>
                            <input type="text" id="name" name="name"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                value="<?php echo $user; ?>" required readonly />

                        </div>
                        <input type="hidden" id="product_uuid" name="product_uuid"
                            value="<?php echo $_GET['subscription']; ?>">

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Your email
                            </label>
                            <input type="email" id="email" name="email"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                value="<?php echo $student['email']; ?>" required />
                        </div>

                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <label for="select-country-input-3"
                                    class="block text-sm font-medium text-gray-900 dark:text-white"> Phone </label>
                            </div>

                            <input type="text" id="phone" name="phone"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                                value="<?php echo $student['phone']; ?>" required />
                        </div>


                    </div>
                </div>
                <div
                    class="mt-6 w-full space-y-6 sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-md border border-gray-500 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-400 dark:divide-gray-500">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">
                                    $<?php echo $product['cost']; ?></dd>
                            </dl>
                            <input type="hidden" name="amount" value="<?php echo $product['cost'] * 0.5; ?>" />
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subscription</dt>
                                <dd class="text-base font-medium text-green-500"><?php echo $product['name']; ?></dd>
                                <input type="hidden" id="product" name="product"
                                    value="<?php echo $product['name']; ?>" />
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Duration (days)</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">
                                    <?php echo $product['duration']; ?>
                                    <input type="hidden" id="duration" name="duration"
                                        value="<?php echo $product['duration']; ?>" />
                                </dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Discount</dt>
                                <dd class="text-base text-gray-900 dark:text-white">
                                    50%</dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-bold text-gray-900 dark:text-white">
                                    $<?php $cost = $product['cost'];
                                    echo $cost * 0.5;
                                    ?></dd>
                            </dl>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit"
                            class="text-white w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Proceed
                            to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>