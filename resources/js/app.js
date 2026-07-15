import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

document.addEventListener('alpine:init', () => {
    Alpine.data('scrollSpy', () => ({
        active: 'home',
        init() {
            window.addEventListener('scroll', () => {
                const ids = ['features', 'about', 'home'];
                for (const id of ids) {
                    const el = document.getElementById(id);
                    if (el && el.getBoundingClientRect().top <= 150) {
                        this.active = id;
                        break;
                    }
                }
            }, { passive: true });
        }
    }));

    Alpine.data('saleForm', (customers) => ({
        paymentStatus: 'belum',
        step: 'form',
        validationMsg: '',
        customerId: '',
        customerName: '',
        customerSearch: '',
        showCustomerDropdown: false,
        showQuickAdd: false,
        newCustomerName: '',
        newCustomerPhone: '',
        newCustomerAddress: '',
        quickAddError: '',
        quickAddSubmitting: false,
        customers: customers || [],
        salesDate: '',
        cancelConfirm: false,
        items: [],
        rowId: 0,

        init() {
            this.addRow();
            this.salesDate = new Date().toISOString().slice(0, 10);
            this.$watch('customerSearch', (val) => {
                if (this.customerName && val !== this.customerName) {
                    this.customerId = '';
                    this.customerName = '';
                }
                this.showQuickAdd = false;
            });
        },

        get filteredCustomers() {
            if (!this.customerSearch) return this.customers;
            const q = this.customerSearch.toLowerCase();
            return this.customers.filter(c => c.name.toLowerCase().includes(q));
        },

        selectCustomer(customer) {
            this.customerId = customer.id;
            this.customerName = customer.name;
            this.customerSearch = customer.name;
            this.showCustomerDropdown = false;
            this.showQuickAdd = false;
        },

        async submitQuickAdd() {
            this.quickAddError = '';
            if (!this.newCustomerName.trim()) {
                this.quickAddError = 'Nama customer harus diisi';
                return;
            }
            this.quickAddSubmitting = true;
            try {
                const res = await fetch('/customers/quick-add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                    },
                    body: JSON.stringify({
                        name: this.newCustomerName,
                        phone: this.newCustomerPhone,
                        address: this.newCustomerAddress,
                    }),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.quickAddError = Object.values(err.errors || { 'error': 'Gagal simpan' }).flat().join(', ');
                    return;
                }
                const customer = await res.json();
                this.customers.push(customer);
                this.selectCustomer(customer);
                this.newCustomerName = '';
                this.newCustomerPhone = '';
                this.newCustomerAddress = '';
            } catch (e) {
                this.quickAddError = 'Terjadi kesalahan, coba lagi';
            } finally {
                this.quickAddSubmitting = false;
            }
        },

        addRow() {
            this.items.push({
                id: ++this.rowId,
                product_id: '',
                quantity: 1,
                product_price: 0,
                product_stock: 0,
                product_name: '',
            });
        },

        removeRow(id) {
            if (this.items.length > 1) {
                this.items = this.items.filter(item => item.id !== id);
            }
        },

        setProduct(row, productId) {
            const item = this.items.find(i => i.id === row.id);
            if (!item) return;
            item.product_id = productId;
            const opt = document.getElementById('product-opt-' + productId);
            if (opt) {
                item.product_price = parseFloat(opt.dataset.price) || 0;
                item.product_stock = parseInt(opt.dataset.stock) || 0;
                item.product_name = opt.dataset.name || '';
            }
        },

        subtotal(row) {
            return row.product_price * (row.quantity || 0);
        },

        get total() {
            return this.items.reduce((sum, row) => sum + this.subtotal(row), 0);
        },

        stockStatus(row) {
            if (!row.product_id) return '';
            if (row.product_stock <= 0) return 'Stok habis';
            return row.product_stock >= row.quantity
                ? 'Tersedia: ' + row.product_stock
                : 'Stok hanya ' + row.product_stock;
        },

        validate() {
            const empty = this.items.some(i => !i.product_id || i.quantity < 1);
            if (empty) {
                this.validationMsg = 'Lengkapi semua produk (pilih produk dan isi jumlah)';
                return false;
            }
            this.validationMsg = '';
            return true;
        },

        goConfirm() {
            if (!this.validate()) return;
            this.step = 'confirm';
        },

        submitForm() {
            this.$refs.form.submit();
        },

        confirmCancel() {
            this.cancelConfirm = true;
        },

        dismissCancel() {
            this.cancelConfirm = false;
        },

        closeModal() {
            this.cancelConfirm = false;
            this.step = 'form';
            this.$dispatch('close-modal', 'add-sale');
        },

        confirmBeforeClose() {
            if (this.step === 'confirm') {
                this.step = 'form';
                return;
            }
            const hasInput = this.items.some(i => i.product_id || i.quantity > 1);
            if (hasInput) {
                this.cancelConfirm = true;
            } else {
                this.closeModal();
            }
        }
    }));
});

Alpine.start();

function updateNavClock() {
    const now = new Date();
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    let h = now.getHours();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    const m = String(now.getMinutes()).padStart(2,'0');
    const day = days[now.getDay()];
    const d = now.getDate();
    const month = months[now.getMonth()];
    const suffix = d === 1 ? 'st' : d === 2 ? 'nd' : d === 3 ? 'rd' : 'th';

    const navTime = document.getElementById('navTime');
    const navAmPm = document.getElementById('navAmPm');
    const navDay = document.getElementById('navDay');

    if (navTime) navTime.textContent = h + ':' + m;
    if (navAmPm) navAmPm.textContent = ampm;
    if (navDay) navDay.textContent = day + ', ' + month + ' ' + d + suffix;
}

document.addEventListener('DOMContentLoaded', function() {
    updateNavClock();
    setInterval(updateNavClock, 10000);
});