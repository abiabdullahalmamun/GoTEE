<template>
    <div>
        <div id="paymentInputDiv">
            <div class="form-group">
                <label for="number_of_month">Number of Months</label>
                <input
                    type="number"
                    id="number_of_month"
                    v-model.number="numberOfMonths"
                    @input="calculateTotal"
                    class="form-control"
                    min="1"
                >
            </div>

            <div class="form-group">
                <label for="bill_adjustments">Bill Adjustments</label>
                <input
                    type="number"
                    id="bill_adjustments"
                    v-model.number="billAdjustments"
                    @input="calculateTotal"
                    class="form-control"
                >
            </div>

            <div id="output" v-show="showOutput">
                <p id="show_value">সর্বমোট {{ totalAmount }} টাকার বিল পরিশোধ করছেন</p>
            </div>

            <input type="hidden" id="tranxAmount" v-model="totalAmount">

            <!-- Member fields (hidden or visible as needed) -->
            <input type="hidden" id="member_id" v-model="member.id">
            <input type="hidden" id="member_name" v-model="member.name">
            <input type="hidden" id="member_email" v-model="member.email">
            <input type="hidden" id="member_phone" v-model="member.phone">
            <input type="hidden" id="member_payment_by" v-model="member.payment_by">
            <input type="hidden" id="member_address" v-model="member.address">
            <input type="hidden" id="member_currency" value="BDT">
            <input type="hidden" id="member_transaction_id" :value="transactionId">
            <input type="hidden" id="member_status" value="pending">

            <button
                id="pay_now_button"
                @click="initiatePayment"
                :disabled="!paymentEnabled"
                class="btn btn-primary"
            >
                Pay Now
            </button>
        </div>

        <!-- Modal for TAP iFrame -->
        <div v-if="showModal" class="tap-modal-container">
            <div class="tap-modal-content">
                <iframe
                    id="tap-paymentIframe"
                    :src="iframeSrc"
                    title="TAP Payment Gateway"
                    frameborder="0"
                ></iframe>
                <button @click="closeModal" class="btn btn-danger close-button">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        memberData: {
            type: Object,
            default: () => ({
                id: '',
                name: '',
                email: '',
                phone: '',
                payment_by: '',
                address: ''
            })
        }
    },

    data() {
        return {
            numberOfMonths: 0,
            billAdjustments: 0,
            totalAmount: 0,
            showOutput: false,
            paymentEnabled: false,
            showModal: false,
            iframeSrc: '',
            storedPaymentId: null,
            paymentProcessed: false,
            transactionId: this.generateTransactionId(),
            monthlyBill: 200,
            homeUrl: "https://merchant-pg-ui-prod.tadlbd.com",
            authSettings: {
                url: "https://auth-prod.tadlbd.com/oauth/token",
                headers: {
                    Authorization: "Basic QVJNT0M6QVJNT0NAQVJNT0MjJDg5NzY=",
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                data: "grant_type=password&username=ARMOC-User&password=" +
                    encodeURIComponent("ARMOC@#0989SHARMOC")
            }
        };
    },

    computed: {
        member() {
            return this.memberData;
        }
    },

    mounted() {
        window.addEventListener('message', this.handleMessage);
    },

    beforeDestroy() {
        window.removeEventListener('message', this.handleMessage);
    },

    methods: {
        calculateTotal() {
            if (this.numberOfMonths > 0) {
                this.showOutput = true;
                this.paymentEnabled = true;
                this.totalAmount = (this.numberOfMonths * this.monthlyBill) + this.billAdjustments;
            } else {
                this.showOutput = false;
                this.paymentEnabled = false;
            }
        },

        generateTransactionId() {
            return 'TXN-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        },

        async initiatePayment() {
            if (!this.paymentEnabled) return;

            this.openModal();
            await this.getAuthToken();
        },

        openModal() {
            this.showModal = true;
            this.iframeSrc = `${this.homeUrl}/tap.html`;
        },

        closeModal() {
            this.showModal = false;
            this.paymentProcessed = false;
        },
        async getAuthToken() {
            try {
                const response = await fetch(this.authSettings.url, {
                    method: 'POST',
                    headers: {
                        'Authorization': this.authSettings.headers.Authorization,
                        'Content-Type': this.authSettings.headers['Content-Type']
                    },
                    body: this.authSettings.data
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const authResponse = await response.json();
                await this.storePayment(authResponse);
            } catch (error) {
                console.error('Error getting auth token:', error);
                this.closeModal();
                console.log('Payment initiation failed. Please try again.');
            }
        },

        async storePayment(authResponse) {
            const paymentData = {
                // (Keep your existing paymentData structure)
            };

            try {
                // const response = await fetch(this.$route('user.user-tap-payment-store'), {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify(paymentData)
                // });
                //
                // if (!response.ok) {
                //     throw new Error(`Payment storage failed with status: ${response.status}`);
                // }

                // const data = await response.json();
                // if (!data.payment_id) {
                //     throw new Error('Invalid payment ID received from server');
                // }

                // this.storedPaymentId = data.payment_id;
                this.initiateIframe(authResponse);
            } catch (error) {
                console.error('Error storing payment:', error);
                this.closeModal();
                console.log('Error processing payment. ' + error.message);
            }
        },

        initiateIframe(authResponse) {
            const paymentParams = {
                // (Keep your existing paymentParams structure)
            };

            const iframe = document.getElementById('tap-paymentIframe');
            if (iframe && iframe.contentWindow) {
                iframe.onload = () => {
                    try {
                        iframe.contentWindow.postMessage(JSON.stringify(paymentParams), '*');
                    } catch (error) {
                        console.error('Error posting message to iframe:', error);
                        this.closeModal();
                        console.log('Failed to initialize payment gateway.');
                    }
                };
            }
        },

        handleMessage(event) {
            // Skip if origin is not trusted (add your payment gateway domain)
            // if (event.origin !== "https://merchant-pg-ui-prod.tadlbd.com") return;

            try {
                // First try to parse if it's a string
                let data = event.data;
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (parseError) {
                        // If parsing fails, it might be already an object
                        if (typeof data !== 'object') {
                            console.warn('Received non-JSON message:', data);
                            return;
                        }
                    }
                }

                // Ensure we have valid data
                if (!data || typeof data !== 'object') {
                    console.warn('Invalid message format:', data);
                    return;
                }

                if (data.status === "completed" && !this.paymentProcessed) {
                    this.paymentProcessed = true;
                    this.updatePaymentStatus(data);
                }
            } catch (error) {
                console.error('Error handling message:', error, 'Original event data:', event.data);
                // Don't show error to user for message handling failures
            }
        },

        async updatePaymentStatus(paymentResponse) {
            if (!paymentResponse.transactionId) {
                console.error('Invalid payment response - missing transactionId:', paymentResponse);
                console.log('Payment verification failed. Please contact support.');
                this.closeModal();
                return;
            }

            const paymentData = {
                // (Keep your existing paymentData structure)
            };

            try {
                // const response = await fetch(this.$route('user.user-tap-payment-store'), {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify(paymentData)
                // });
                //
                // if (!response.ok) {
                //     throw new Error(`Payment status update failed with status: ${response.status}`);
                // }
                //
                // const data = await response.json();
                console.log('Payment successfully processed!');

                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } catch (error) {
                console.error('Error updating payment status:', error);
                console.log('Payment completed but status update failed. ' + error.message);
            } finally {
                this.closeModal();
            }
        }
    }
};
</script>

<style scoped>
.tap-modal-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.tap-modal-content {
    position: relative;
    width: 415px;
    height: 600px;
    background-color: white;
}

#tap-paymentIframe {
    width: 100%;
    height: 100%;
    border: none;
}

.close-button {
    position: absolute;
    top: -40px;
    right: 0;
    padding: 5px 10px;
}

.form-group {
    margin-bottom: 15px;
}

#output {
    margin: 15px 0;
}
</style>
