const { createApp } = Vue;
// B U I L T B Y A B D U R R A H M A N

createApp({
    data: () => ({
        isInitialLoading: !0,
        currentUser: null,
        serverTimeOffset: 0,
        authView: "login",
        authMessage: "",
        messageColor: "bg-red-50 text-red-600 border border-red-100",
        authForm: { username: "", password: "" },
        showPassword: !1,
        forgotForm: { username: "", email: "", newPassword: "", confirmPassword: "" },
        firstLoginUser: null,
        firstLoginForm: { newPassword: "", confirmPassword: "" },
        profileUpdate: { currentPass: "", newPass: "", confirmPass: "", newEmail: "", confirmEmail: "" },// <!-- B UI L T BY A B D U R R A H M A N -->
        singleForm: { tag: "", type: "", brand: "", model: "", serial: "", purchaseDate: "" },
        adminAddUserForm: { username: "", email: "", password: "", role: "Maintenance" },
        users: [],
        activeTab: "dashboard",
        searchQuery: "",
        statusFilter: "",
        assets: [],
        draftRepairs: {},
        currentPage: 1,
        itemsPerPage: 25,
        slots: [],
        slotForm: { sn: "", date_val: "", slotNo: "", slotName: "", totalAssets: "", returnToIT: "", eol: "", pending: "", remarks: "" },
        editingSlotId: null,
        reportSelectCategory: "All",
        reportSelectMonthFrom: "All",
        reportSelectMonthTo: "All",
        reportSelectYear: "All",
        appliedReportCategory: "All",
        appliedReportMonthFrom: "All",
        appliedReportMonthTo: "All",
        appliedReportYear: "All",
        showMonthDetailModal: !1,
        selectedMonthDetail: { monthName: "", year: 0, month: 0, items: [] },
        showItemDetailModal: !1,
        selectedItemDetail: { itemName: "", monthName: "", year: 0, assets: [] },
        statusOptions: ["Received for Repair", "In Assessment", "Quick Repair Stage", "Complex Stage", "Ready", "Irreparable", "Delivered To IT", "EOL/ Disposed", "N/A"],
        heartbeatTimer: null,
        isBulkProcessing: !1,
        bulkDuplicates: [],
        showBulkDuplicateModal: !1,
        accessoriesSearchQuery: "",
        accessoriesSearchResults: [],
        accessoriesSearchTimeout: null,
        syncChannel: null,
        recoveryCodeInput: "",
    }),
    async mounted() {
        this.initSyncChannel();
        await this.loadData();
        const e = sessionStorage.getItem("activeUserV2");// <!-- B UI L T BY A B D U R R A H M A N -->
        if (e) {
            this.currentUser = JSON.parse(e);
            this.startHeartbeat();
        }
        setTimeout(() => {
            this.isInitialLoading = !1;
        }, 800);
    },
    watch: {
        searchQuery() { this.currentPage = 1; },
        statusFilter() { this.currentPage = 1; },
        accessoriesSearchQuery(newVal) {
            clearTimeout(this.accessoriesSearchTimeout);
            this.accessoriesSearchTimeout = setTimeout(() => {
                if (newVal && newVal.trim() !== "") {
                    this.searchAccessories();
                } else {
                    this.accessoriesSearchResults = [];
                }
            }, 300);
        }
    },
    computed: {
        activeUsers() { return this.users.filter((e => "pending" !== e.status)) },
        pendingUsers() { return this.users.filter((e => "pending" === e.status)) },
        totalActiveUserCount() { return this.activeUsers.length },
        inventoryAssets() { return this.assets.filter((e => "Pending Approval" !== e.status)) },
        pendingAssets() { return this.assets.filter((e => "Pending Approval" === e.status)) },
        filteredAssets() {
            let e = this.inventoryAssets;
            if (this.searchQuery.trim()) {
                const t = this.searchQuery.toLowerCase();
                return e.filter((e => e.tag.toLowerCase().includes(t) || e.serial.toLowerCase().includes(t)))
            }
            return this.statusFilter && (e = e.filter((e => e.status === this.statusFilter))), e
        },
        totalPages() { return Math.ceil(this.filteredAssets.length / this.itemsPerPage) || 1 },// <!-- B UI L T BY A B D U R R A H M A N -->
        paginatedAssets() {
            const e = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredAssets.slice(e, e + this.itemsPerPage)
        },
        inRepairCount() {
            return this.inventoryAssets.filter((e => ["Received for Repair", "In Assessment", "Quick Repair Stage", "Complex Stage", "Ready"].includes(e.status))).length
        },
        deliveredCount() {
            return this.inventoryAssets.reduce(((e, t) => e + parseInt(t.deliveryCount || 0, 10)), 0)
        },
        statusCounts() {
            const e = {};
            return this.inventoryAssets.forEach((t => { e[t.status] = (e[t.status] || 0) + 1 })), e
        },
        allTimeRepairedCount() {
            return this.inventoryAssets.reduce(((e, t) => e + parseInt(t.repairCount || 0, 10)), 0)
        },
        duplicateAssetWarning() {
            return !(!this.singleForm.tag && !this.singleForm.serial) && this.assets.some((e => this.singleForm.tag && e.tag.toLowerCase() === this.singleForm.tag.toLowerCase() || this.singleForm.serial && e.serial.toLowerCase() === this.singleForm.serial.toLowerCase()))
        },
        slotTotals() {
            return this.slots.reduce(((e, t) => (e.totalAssets += parseInt(t.totalAssets || 0, 10), e.returnToIT += parseInt(t.returnToIT || 0, 10), e.eol += parseInt(t.eol || 0, 10), e.pending += parseInt(t.pending || 0, 10), e)), {
                totalAssets: 0, returnToIT: 0, eol: 0, pending: 0
            })
        },
        availableCategories() {
            const set = new Set();
            this.inventoryAssets.forEach(s => {
                if (s.repairs && s.repairs.length) {
                    s.repairs.forEach(a => {
                        const name = (a.problem || "").trim().toUpperCase();// <!-- B UI L T BY A B D U R R A H M A N -->
                        if (name) set.add(name);
                    });
                }
            });
            return Array.from(set).sort();
        },
        monthlyReportData() {
            const e = {},
                t = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            this.inventoryAssets.forEach((s => {
                if (s.repairs && s.repairs.length) {
                    const r = new Set();
                    s.repairs.forEach((a => {
                        const prob = (a.problem || "Unspecified Part").trim().toUpperCase();
                        if ("All" !== this.appliedReportCategory && prob !== this.appliedReportCategory.toUpperCase()) {
                            return;
                        }
                        const o = new Date(a.date);
                        if (isNaN(o.getTime())) return;
                        const i = o.getFullYear(),
                            n = o.getMonth() + 1,
                            l = o.getDate(),
                            h = `${i}-${n.toString().padStart(2,"0")}`,
                            d = `${h}-${l}`;
                        e[h] || (e[h] = {
                            key: h,
                            month: n,
                            year: i,
                            monthName: t[n - 1],
                            assetCount: 0,
                            cost: 0,
                            uniqueAssetsSet: new Set()
                        }), e[h].cost += parseFloat(a.cost) || 0, e[h].uniqueAssetsSet.add(s.tag), r.has(d) || (e[h].assetCount += 1, r.add(d))
                    }))
                }
            }));
            let s = Object.values(e).map((e => (e.uniqueAssetCount = e.uniqueAssetsSet.size, e))).sort(((e, t) => t.key.localeCompare(e.key)));
            if ("All" !== this.appliedReportYear && (s = s.filter((e => e.year === parseInt(this.appliedReportYear)))), "All" !== this.appliedReportMonthFrom && "All" !== this.appliedReportMonthTo) {
                let e = parseInt(this.appliedReportMonthFrom),
                    t = parseInt(this.appliedReportMonthTo),// <!-- B UI L T BY A B D U R R A H M A N -->
                    r = Math.min(e, t),
                    a = Math.max(e, t);
                s = s.filter((e => e.month >= r && e.month <= a))
            } else "All" !== this.appliedReportMonthFrom ? s = s.filter((e => e.month >= parseInt(this.appliedReportMonthFrom))) : "All" !== this.appliedReportMonthTo && (s = s.filter((e => e.month <= parseInt(this.appliedReportMonthTo))));
            return s
        },
        isMonthlyFilterActive() {
            return this.appliedReportCategory !== 'All' ||
                   this.appliedReportMonthFrom !== 'All' ||
                   this.appliedReportMonthTo !== 'All' ||
                   this.appliedReportYear !== 'All';
        },
        totalFilteredMonthlyCost() {
            return this.monthlyReportData.reduce((sum, row) => sum + (parseFloat(row.cost) || 0), 0);
        }
    },
    methods: {
        apiFetch(url, options = {}) {
            const token = sessionStorage.getItem("authToken");
            const headers = options.headers ? new Headers(options.headers) : new Headers();
            if (token) {
                headers.set("X-Auth-Token", token);
                headers.set("Authorization", `Bearer ${token}`);
            }
            options.headers = headers;
            options.credentials = 'same-origin';
            return fetch(url, options);
        },
        initSyncChannel() {
            if ('BroadcastChannel' in window) {
                this.syncChannel = new BroadcastChannel('assetcare_realtime_channel');// <!-- B UI L T BY A B D U R R A H M A N -->
                this.syncChannel.onmessage = (event) => {
                    if (event.data && event.data.action === 'reload') {
                        this.loadData();
                    }
                };
            }
        },
        notifyOtherWindows() {
            if (this.syncChannel) {
                this.syncChannel.postMessage({ action: 'reload', time: Date.now() });
            }
        },
        searchAccessories() {
            const query = this.accessoriesSearchQuery.trim().toLowerCase();
            this.accessoriesSearchResults = [];
            if (!query) return;
            
            this.inventoryAssets.forEach(asset => {
                if (asset.repairs && asset.repairs.length) {
                    asset.repairs.forEach(repair => {
                        if (repair.repSerial && repair.repSerial.toLowerCase() === query) {
                            this.accessoriesSearchResults.push({
                                type: repair.problem || 'N/A',
                                serial: repair.repSerial,
                                assetTag: asset.tag,
                                warranty: repair.warrantyMonths ? repair.warrantyMonths + ' months' : 'N/A',
                                purchaseDate: repair.accDate || 'N/A'
                            });
                        }
                    });
                }
            });
        },
        clearAccessoriesSearch() {
            this.accessoriesSearchQuery = "";
            this.accessoriesSearchResults = [];// <!-- B UI L T BY A B D U R R A H M A N -->
        },
        openMonthDetails(e) {
            const t = {};
            this.inventoryAssets.forEach((s => {
                s.repairs && s.repairs.length && s.repairs.forEach((s => {
                    const prob = (s.problem || "Unspecified Part").trim().toUpperCase();
                    if ("All" !== this.appliedReportCategory && prob !== this.appliedReportCategory.toUpperCase()) return;
                    const r = new Date(s.date);
                    if (!isNaN(r.getTime()) && r.getFullYear() === e.year && r.getMonth() + 1 === e.month) {
                        const a = parseFloat(s.cost) || 0;
                        t[prob] || (t[prob] = { item: prob, quantity: 0, amount: 0 }), t[prob].quantity += 1, t[prob].amount += a
                    }
                }))
            })), this.selectedMonthDetail = {
                monthName: e.monthName,
                year: e.year,
                month: e.month,
                items: Object.values(t).sort(((e, t) => t.amount - e.amount))
            }, this.showMonthDetailModal = !0
        },
        closeMonthDetailModal() { this.showMonthDetailModal = !1 },
        openItemDetails(e) {
            const t = {},
                s = e.item.trim().toUpperCase(),
                r = this.selectedMonthDetail.month,
                a = this.selectedMonthDetail.year;
            this.inventoryAssets.forEach((e => {
                e.repairs && e.repairs.length && e.repairs.forEach((o => {
                    const i = new Date(o.date);
                    if (!isNaN(i.getTime()) && i.getFullYear() === a && i.getMonth() + 1 === r) {
                        if ((o.problem || "Unspecified Part").trim().toUpperCase() === s) {
                            const costVal = parseFloat(o.cost) || 0;// <!-- B UI L T BY A B D U R R A H M A N -->
                            t[e.tag] || (t[e.tag] = { tag: e.tag, quantity: 0, cost: 0 }), t[e.tag].quantity += 1, t[e.tag].cost += costVal
                        }
                    }
                }))
            })), this.selectedItemDetail = {
                itemName: e.item,
                monthName: this.selectedMonthDetail.monthName,
                year: this.selectedMonthDetail.year,
                assets: Object.values(t).sort(((e, t) => t.cost - e.cost))
            }, this.showItemDetailModal = !0
        },
        closeItemDetailModal() { this.showItemDetailModal = !1 },
        exportMonthDetailsCSV() {
            if (!this.selectedMonthDetail.items || !this.selectedMonthDetail.items.length) return alert("No items to export.");
            const rows = this.selectedMonthDetail.items.map((e, idx) => `${idx + 1},"${e.item.replace(/"/g, '""')}",${e.quantity},${e.amount}`);
            const csvContent = "\uFEFFIndex,Item / Description,Quantity,Total Amount (Tk)\n" + rows.join("\n");
            const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `AssetCare_Parts_Summary_${this.selectedMonthDetail.monthName}_${this.selectedMonthDetail.year}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        },
        exportItemDetailsCSV() {
            if (!this.selectedItemDetail.assets || !this.selectedItemDetail.assets.length) return alert("No replacement asset details to export.");
            const rows = this.selectedItemDetail.assets.map((e, idx) => `${idx + 1},${e.tag},${e.quantity},${e.cost}`);
            const csvContent = "\uFEFFIndex,Asset Tag,Quantity,Cost Amount (Tk)\n" + rows.join("\n");
            const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `AssetCare_${this.selectedItemDetail.itemName.replace(/[^a-zA-Z0-9]/g, '_')}_Replacement_${this.selectedMonthDetail.monthName}_${this.selectedMonthDetail.year}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        },
        applyMonthlyFilter() {
            this.appliedReportCategory = this.reportSelectCategory;
            this.appliedReportMonthFrom = this.reportSelectMonthFrom;
            this.appliedReportMonthTo = this.reportSelectMonthTo;// <!-- B UI L T BY A B D U R R A H M A N -->
            this.appliedReportYear = this.reportSelectYear;
        },
        clearMonthlyFilter() {
            this.reportSelectCategory = "All";
            this.reportSelectMonthFrom = "All";
            this.reportSelectMonthTo = "All";
            this.reportSelectYear = "All";
            this.appliedReportCategory = "All";
            this.appliedReportMonthFrom = "All";
            this.appliedReportMonthTo = "All";
            this.appliedReportYear = "All";
        },
        exportFilteredMonthlyCSV() {
            if (!this.monthlyReportData || !this.monthlyReportData.length) return alert("No monthly data to export for selected filter.");
            
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const monthFromText = this.appliedReportMonthFrom === 'All' ? 'All' : (monthNames[parseInt(this.appliedReportMonthFrom) - 1] || this.appliedReportMonthFrom);
            const monthToText = this.appliedReportMonthTo === 'All' ? 'All' : (monthNames[parseInt(this.appliedReportMonthTo) - 1] || this.appliedReportMonthTo);

            let csvContent = "\uFEFF";
            
            if (this.isMonthlyFilterActive) {
                csvContent += "=== APPLIED FILTER SUMMARY ===\n";
                csvContent += `Category Filter:,${this.appliedReportCategory}\n`;
                csvContent += `Month From:,${monthFromText}\n`;
                csvContent += `Month To:,${monthToText}\n`;
                csvContent += `Year:,${this.appliedReportYear}\n`;
                csvContent += `Total Filtered Cost (Tk):,${this.totalFilteredMonthlyCost}\n\n`;
            }
            csvContent += "=== MONTHLY OVERVIEW ===\n";
            csvContent += "Month & Year,Total Repaired,Unique Assets Repaired,Total Monthly Cost (Tk)\n";
            this.monthlyReportData.forEach(row => {
                csvContent += `"${row.monthName} ${row.year}",${row.assetCount},${row.uniqueAssetCount},${row.cost}\n`;// <!-- B UI L T BY A B D U R R A H M A N -->
            });
            csvContent += "\n=== DETAILED PARTS & REPLACEMENT RECORDS ===\n";
            csvContent += "Month,Asset Tag,Type,Brand / Model,Replaced Part,Part Serial Number,Cost Amount (Tk),Date\n";

            this.monthlyReportData.forEach(mRow => {
                this.inventoryAssets.forEach(s => {
                    if (s.repairs && s.repairs.length) {
                        s.repairs.forEach(a => {
                            const d = new Date(a.date);
                            if (!isNaN(d.getTime()) && d.getFullYear() === mRow.year && (d.getMonth() + 1) === mRow.month) {
                                const prob = (a.problem || "Unspecified Part").trim().toUpperCase();
                                if (this.appliedReportCategory === 'All' || prob === this.appliedReportCategory.toUpperCase()) {
                                    const c = parseFloat(a.cost) || 0;
                                    const type = (s.type || 'N/A').replace(/"/g, '""');
                                    const brandModel = `${s.brand || ''} ${s.model || ''}`.trim().replace(/"/g, '""');// <!-- B UI L T BY A B D U R R A H M A N -->
                                    const part = prob.replace(/"/g, '""');
                                    const serial = (a.repSerial || 'N/A').replace(/"/g, '""');
                                    
                                    csvContent += `"${mRow.monthName} ${mRow.year}","${s.tag}","${type}","${brandModel}","${part}","${serial}",${c},"${a.date}"\n`;
                                }
                            }
                        });
                    }
                });
            });

            const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });// <!-- B UI L T BY A B D U R R A H M A N -->
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `AssetCare_Monthly_Report_${new Date().toISOString().split("T")[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        },
        async loadData() {
            try {
                const e = await this.apiFetch("api.php?action=load");
                const t = await e.json();
                if (t.success) {
                    this.serverTimeOffset = Date.now() - (t.serverTime || Date.now());
                    this.users = t.users || [];
                    this.assets = (t.assets || []).map((e => ("Pending" === e.status && (e.status = "In Assessment"), e)));// <!-- B UI L T BY A B D U R R A H M A N -->
                    this.slots = t.slots || [];
                } else if (t.authenticated === false && this.currentUser) {
                    this.logout();
                }
            } catch (e) {}
        },
        async saveDbUser(e) { 
            e.portalLink = window.location.origin + window.location.pathname;
            await this.apiFetch("api.php?action=save_user", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(e) 
            });
            this.notifyOtherWindows();
        },
        async deleteDbUser(e) { 
            await this.apiFetch("api.php?action=delete_user", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },// <!-- B UI L T BY A B D U R R A H M A N -->
                body: JSON.stringify({ username: e }) 
            });
            this.notifyOtherWindows();
        },
        async saveDbAsset(e) { 
            await this.apiFetch("api.php?action=save_asset", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(e) 
            });
            this.notifyOtherWindows();
        },
        async deleteDbAsset(e) { 
            await this.apiFetch("api.php?action=delete_asset", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ tag: e }) 
            });
            this.notifyOtherWindows();
        },
        async saveDbSlot(e) { 
            await this.apiFetch("api.php?action=save_slot", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(e) 
            });
            await this.loadData();
            this.notifyOtherWindows();
        },
        async deleteDbSlot(e) { 
            await this.apiFetch("api.php?action=delete_slot", { 
                method: "POST", 
                headers: { "Content-Type": "application/json" },// <!-- B UI L T BY A B D U R R A H M A N -->
                body: JSON.stringify({ id: e }) 
            });
            this.notifyOtherWindows();
        },
        submitSlot() {
            if (confirm("Are you sure for the changes?")) {
                if (!this.slotForm.sn || !this.slotForm.slotNo) return alert("SN and Slot No are required!");
                this.saveDbSlot(this.slotForm);
                this.slotForm = { sn: "", date_val: "", slotNo: "", slotName: "", totalAssets: "", returnToIT: "", eol: "", pending: "", remarks: "" };
            }
        },
        clearSlotForm() {
            confirm("Are you sure for the changes?") && (this.slotForm = { sn: "", date_val: "", slotNo: "", slotName: "", totalAssets: "", returnToIT: "", eol: "", pending: "", remarks: "" })
        },
        deleteSlot(e) { confirm("Are you sure for the changes?") && (this.slots = this.slots.filter((t => t.id !== e)), this.deleteDbSlot(e)) },
        editSlot(e) { this.editingSlotId = e },
        cancelEdit() { confirm("Are you sure for the changes?") && (this.editingSlotId = null, this.loadData()) },
        saveSlotInline(e) { confirm("Are you sure for the changes?") && (this.saveDbSlot(e), this.editingSlotId = null) },
        startHeartbeat() { 
            this.updateHeartbeat();
            if (this.heartbeatTimer) clearInterval(this.heartbeatTimer);
            this.heartbeatTimer = setInterval((() => this.updateHeartbeat()), 2e3);// <!-- B UI L T BY A B D U R R A H M A N -->
        },
        async updateHeartbeat() {
            if (!this.currentUser) return;
            const e = await this.apiFetch("api.php?action=load"),
                t = await e.json();
            if (t.success) {
                this.serverTimeOffset = Date.now() - (t.serverTime || Date.now());
                this.users = t.users;
                this.assets = t.assets.map((e => ("Pending" === e.status && (e.status = "In Assessment"), e)));
                null === this.editingSlotId && (this.slots = t.slots || []);
                const currentInList = this.users.find((e => e.username === this.currentUser.username));
                if (!currentInList) return this.logout(), void setTimeout((() => this.showMessage("Your account has been deleted by an administrator.")), 500);
                if ("blocked" === currentInList.status) return this.logout(), void setTimeout((() => this.showMessage("Your account has been blocked by an administrator.")), 500);
                
                currentInList.role !== this.currentUser.role && (this.currentUser.role = currentInList.role, sessionStorage.setItem("activeUserV2", JSON.stringify(this.currentUser)));
                this.currentUser.mustResetPassword = currentInList.mustResetPassword;
                
                this.apiFetch(`api.php?action=ping`);
            } else if (t.authenticated === false) {
                this.logout();
            }
        },
        isOnline(e) {
            if (!e || !e.lastSeen) return !1;
            const t = parseInt(e.lastSeen, 10);// <!-- B UI L T BY A B D U R R A H M A N -->
            if (t === 0) return !1;
            const adjustedNow = Date.now() - this.serverTimeOffset;
            return Math.abs(adjustedNow - t) < 12000;
        },
        tabClass(e) {
            return this.activeTab === e ? "bg-white text-blue-600 shadow-[0_2px_10px_rgba(0,0,0,0.04)] font-semibold border-transparent" : "text-gray-500 hover:text-gray-800 hover:bg-gray-200/50 font-medium border-transparent"
        },
        statusBg: e => ({ "In Assessment": "bg-yellow-400", "Quick Repair Stage": "bg-blue-400", "Complex Stage": "bg-orange-400", Ready: "bg-green-400", Irreparable: "bg-red-400", "EOL/ Disposed": "bg-gray-500" }[e] || "bg-gray-300"),
        statusColor: e => "Ready" === e ? "bg-green-50 text-green-700 border-green-200" : "Irreparable" === e ? "bg-red-50 text-red-700 border-red-200" : "Delivered To IT" === e ? "bg-gray-100 text-gray-700 border-gray-300" : "bg-blue-50 text-blue-700 border-blue-200",
        calculateTotalRepairCost: e => (e.repairs || []).reduce(((e, t) => e + (parseFloat(t.cost) || 0)), 0),
        calculateAge(e) {
            if (!e || "N/A" === e) return "Unknown";
            const t = new Date(e), s = new Date;
            let r = s.getFullYear() - t.getFullYear(), a = s.getMonth() - t.getMonth();// <!-- B UI L T BY A B D U R R A H M A N -->
            return a < 0 && (r--, a += 12), `${r}Y, ${a}M`
        },
        isValidEmail: e => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e),
        showMessage(e, t = "error") {
            this.authMessage = e, this.messageColor = "success" === t ? "bg-green-50 text-green-600 border border-green-100" : "bg-red-50 text-red-600 border border-red-100", setTimeout((() => { this.authMessage = "" }), 5e3)
        },
        async verifyForgotIdentity() {
            if (!this.forgotForm.username || !this.forgotForm.email) return this.showMessage("Please provide both username and email.");
            
            try {
                const res = await this.apiFetch("api.php?action=request_reset_code", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ username: this.forgotForm.username, email: this.forgotForm.email })
                });
                const data = await res.json();
                
                if (data.success) {
                    this.authView = "verify_code";
                    this.recoveryCodeInput = "";
                    this.showMessage("If account exists, a 6-digit code was sent to your email.", "success");
                } else {
                    this.showMessage(data.message || "Failed to initiate recovery process.");
                }
            } catch (error) {
                this.showMessage("Error connecting to server.");// <!-- B UI L T BY A B D U R R A H M A N -->
            }
        },
        async submitRecoveryCode() {
            if (!this.recoveryCodeInput || this.recoveryCodeInput.length !== 6) {
                return this.showMessage("Please enter the 6-digit code.");
            }

            try {
                const res = await this.apiFetch("api.php?action=verify_reset_code", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ username: this.forgotForm.username, code: this.recoveryCodeInput })
                });
                const data = await res.json();

                if (data.success) {
                    this.authView = "reset";
                    this.showMessage("Code verified successfully. Please set a new password.", "success");
                } else {
                    this.showMessage(data.message || "Invalid or expired security code.");
                }
            } catch (err) {
                this.showMessage("Server verification failed.");
            }
        },
        async resetPassword() {
            if (!confirm("Are you sure for the changes?")) return;
            const { newPassword: e, confirmPassword: t, username: s } = this.forgotForm;
            if (!e || e.length < 8) return this.showMessage("Password must be at least 8 characters.");
            if (e !== t) return this.showMessage("Passwords do not match.");

            const res = await this.apiFetch("api.php?action=reset_password", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ username: s, newPassword: e })
            });
            const data = await res.json();
            if (data.success) {
                this.authView = "login";
                this.forgotForm = { username: "", email: "", newPassword: "", confirmPassword: "" };// <!-- B UI L T BY A B D U R R A H M A N -->
                this.showMessage("Password updated successfully! You can now log in.", "success");
            } else {
                this.showMessage(data.message || "Error updating password.");
            }
        },
        async completeFirstLoginPasswordReset() {
            const { newPassword, confirmPassword } = this.firstLoginForm;
            if (!newPassword || newPassword.length < 8) {
                return this.showMessage("Password must be at least 8 characters long.");
            }
            if (newPassword !== confirmPassword) {
                return this.showMessage("Passwords do not match.");
            }

            const res = await this.apiFetch("api.php?action=reset_password", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ username: this.firstLoginUser.username, newPassword: newPassword })
            });
            const data = await res.json();

            if (data.success) {
                this.currentUser = { ...this.firstLoginUser, mustResetPassword: 0 };
                sessionStorage.setItem("activeUserV2", JSON.stringify(this.currentUser));
                this.firstLoginUser = null;
                this.authView = "login";// <!-- B UI L T BY A B D U R R A H M A N -->
                this.authForm = { username: "", password: "" };
                this.firstLoginForm = { newPassword: "", confirmPassword: "" };
                this.startHeartbeat();
            } else {
                this.showMessage(data.message || "Error resetting password.");
            }
        },
        async changePassword() {
            if (!confirm("Are you sure for the changes?")) return;
            const { currentPass: c, newPass: e, confirmPass: t } = this.profileUpdate;
            if (!c) return alert("Please enter your current password.");
            if (!e || e.length < 8) return alert("Password must be at least 8 characters long.");
            if (e !== t) return alert("New passwords do not match.");

            const res = await this.apiFetch("api.php?action=change_password", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    currentPass: c,
                    newPass: e
                })
            });
            const data = await res.json();
            if (data.success) {
                this.profileUpdate.currentPass = "";
                this.profileUpdate.newPass = "";
                this.profileUpdate.confirmPass = "";
                alert("Password updated successfully!");
            } else {
                alert(data.message || "Error updating password.");
            }
        },
        changeEmail() {
            if (this.currentUser.role !== 'admin') {
                return alert("Unauthorized: Only admins can change email addresses.");
            }
            if (!confirm("Are you sure for the changes?")) return;
            const { newEmail: e, confirmEmail: t } = this.profileUpdate;
            if (!e || !/^[\w-\.]+@quantanite\.com$/i.test(e)) return alert("Warning: Invalid domain! Only @quantanite.com addresses are allowed.");
            if (e !== t) return alert("Warning: The new email and confirm email addresses do not match!");
            const s = this.users.findIndex((e => e.username === this.currentUser.username)); 
            -1 !== s && (this.users[s].email = e, this.saveDbUser(this.users[s]), this.currentUser.email = e, sessionStorage.setItem("activeUserV2", JSON.stringify(this.currentUser)), this.profileUpdate.newEmail = "", this.profileUpdate.confirmEmail = "", alert("Email updated successfully!"))
        },
        deleteUser(e) { confirm("Are you sure for the changes?") && (this.users = this.users.filter((t => t.username !== e)), this.deleteDbUser(e)) },// <!-- B UI L T BY A B D U R R A H M A N -->
        changeUserRole(e, t, s) {
            if (!confirm("Are you sure for the changes?")) return void(s && (s.target.value = this.users.find((t => t.username === e)).role));
            const r = this.users.find((t => t.username === e));
            r && (r.role = t, this.saveDbUser(r))
        },
        toggleUserBlock(e) {
            if (!confirm("Are you sure for the changes?")) return;
            const t = this.users.find((t => t.username === e));
            t && (t.status = "blocked" === t.status ? "active" : "blocked", this.saveDbUser(t))
        },
        async adminCreateUser() {
            if (!confirm("Are you sure for the changes?")) return;
            const e = this.adminAddUserForm;
            if (!e.username || !e.password || !e.email) return alert("All fields are required.");
            if (e.password.length < 8) return alert("Initial password must be at least 8 characters.");
            if (!/^[\w-\.]+@quantanite\.com$/i.test(e.email)) return alert("Email must end with @quantanite.com");// <!-- BUILT BY A B D U R R A H M A N -->
            if (this.users.find(t => t.username.toLowerCase() === e.username.toLowerCase() || t.email.toLowerCase() === e.email.toLowerCase())) 
                { return alert("Username or email already exists.");}
            
            const t = { 
                username: e.username, 
                email: e.email, 
                password: e.password, 
                role: e.role, 
                status: "active", 
                requestDate: (new Date).toLocaleString(), 
                lastSeen: 0,
                mustResetPassword: 1
            };
            await this.saveDbUser(t);
            await this.loadData();

            this.adminAddUserForm = { username: "", email: "", password: "", role: "Maintenance" };
            alert("User account created successfully! A welcome email has been sent requiring them to reset their password.");// <!-- B UI L T BY A B D U R R A H M A N -->
        },
        async login() {
            if (!this.authForm.username || !this.authForm.password) return this.showMessage("Please fill all fields.");
            
            try {
                const response = await fetch("api.php?action=login", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: 'same-origin',// <!-- B UI L T BY A B D U R R A H M A N -->
                    body: JSON.stringify({
                        username: this.authForm.username,
                        password: this.authForm.password
                    })
                });
                const t = await response.json();
                
                if (t.success) {
                    const user = t.user;
                    
                    sessionStorage.setItem("authToken", user.api_token);
                    
                    if (parseInt(user.mustResetPassword, 10) === 1 || user.mustResetPassword === true) {
                        this.firstLoginUser = user;
                        this.firstLoginForm = { newPassword: "", confirmPassword: "" };
                        this.authView = "force_reset";
                        this.showMessage("First login detected. You must reset your password to access the portal.", "error");// <!-- B UI L T BY A B D U R R A H M A N -->
                        return;
                    }

                    this.currentUser = user;
                    sessionStorage.setItem("activeUserV2", JSON.stringify(user));
                    this.startHeartbeat();
                    await this.loadData();
                } else {
                    this.showMessage(t.message || "Invalid Username or Password.");
                }
            } catch (err) {
                this.showMessage("Login failed. Could not reach server.");
            }
        },
        async logout() { 
            if (this.currentUser) {
                await this.apiFetch(`api.php?action=logout`);
            }
            clearInterval(this.heartbeatTimer);
            this.currentUser = null;
            sessionStorage.removeItem("activeUserV2");
            sessionStorage.removeItem("authToken");
            this.activeTab = "dashboard";
            this.notifyOtherWindows();
        },
        addSingleDevice() {
            if (!confirm("Are you sure for the changes?")) return;
            if (this.duplicateAssetWarning) return alert("Cannot add device: Tag or Serial number already exists.");// <!-- B UI L T BY A B D U R R A H M A N -->
            const e = { ...this.singleForm, status: "N/A", repairs: [], repairCount: 0, deliveryCount: 0 };
            this.assets.push(e), this.saveDbAsset(e), this.singleForm = { tag: "", type: "", brand: "", model: "", serial: "", purchaseDate: "" }, this.activeTab = "inventory"
        },
        approveAsset(e) {
            if (!confirm("Are you sure for the changes?")) return;
            const t = this.assets.find((t => t.tag === e));
            t && (t.status = "N/A", this.saveDbAsset(t), alert(`Asset ${e} approved and added to inventory!`))
        },
        rejectAsset(e) { confirm("Are you sure for the changes?") && (this.assets = this.assets.filter((t => t.tag !== e)), this.deleteDbAsset(e)) },// <!-- B UI L T BY A B D U R R A H M A N -->
        confirmStatusChange(e, t) {
            confirm("Are you sure for the changes?") ? (t.status = e.target.value, this.updateStatus(t)) : e.target.value = t.status
        },
        updateStatus(e) {
            "Ready" === e.status && (e.repairCount = parseInt(e.repairCount || 0, 10) + 1), "Delivered To IT" === e.status && (e.deliveryCount = parseInt(e.deliveryCount || 0, 10) + 1), this.saveDbAsset ? this.saveDbAsset(e) : this.saveDbAssets && this.saveDbAssets()
        },
        getDraft(e) {
            return this.draftRepairs[e] || (this.draftRepairs[e] = { problem: "", repSerial: "", accDate: "", warrantyMonths: "", cost: 0, date: "" }), this.draftRepairs[e]
        },
        addRepair(e) {
            if (!confirm("Are you sure for the changes?")) return;
            const t = this.assets.find((t => t.tag === e)),// <!-- B UI L T BY A B D U R R A H M A N -->
                s = this.draftRepairs[e];
            if (t && s && s.problem) {
                let r;
                if (s.date) {
                    const [e, t, a] = s.date.split("-");
                    r = `${t}/${a}/${e}`
                } else r = (new Date).toLocaleDateString();
                t.repairs.push({ ...s, date: r }), this.draftRepairs[e] = { problem: "", repSerial: "", cost: 0, date: "" }, this.saveDbAsset ? this.saveDbAsset(t) : this.saveDbAssets && this.saveDbAssets()
            }
        },
        deleteAsset(e) { confirm("Are you sure for the changes?") && (this.assets = this.assets.filter((t => t.tag !== e)), this.deleteDbAsset(e)) },// <!-- B UI L T BY A B D U R R A H M A N -->
        handleFileUpload(e) {
            const t = e.target.files[0];
            if (!t) return;
            this.isBulkProcessing = !0, this.bulkDuplicates = [];
            const s = new FileReader;
            s.onload = t => {
                const s = t.target.result.split("\n"), r = [], a = [];// <!-- B UI L T BY A B D U R R A H M A N -->
                setTimeout((() => {
                    s.forEach(((e, t) => {
                        if (t > 0 && e.trim()) {
                            const t = e.split(",").map((e => e.trim())), s = t[0], o = t[1], i = t[2], n = t[3], l = t[4], h = t[5],
                                d = this.assets.some((e => s && e.tag.toLowerCase() === s.toLowerCase() || l && e.serial.toLowerCase() === l.toLowerCase())) || r.some((e => s && e.tag.toLowerCase() === s.toLowerCase() || l && e.serial.toLowerCase() === l.toLowerCase())),
                                c = { tag: s, type: o, brand: i, model: n, serial: l, purchaseDate: h, status: "N/A", repairs: [], repairCount: 0, deliveryCount: 0 };
                            d ? a.push(c) : r.push(c)
                        }
                    })), r.length > 0 && (this.assets.push(...r), r.forEach((e => this.saveDbAsset(e)))), this.isBulkProcessing = !1, e.target.value = "", a.length > 0 ? (this.bulkDuplicates = a, this.showBulkDuplicateModal = !0) : r.length > 0 && (this.activeTab = "inventory")
                }), 2e3)
            }, s.readAsText(t)
        },
        closeDuplicateModal() { this.showBulkDuplicateModal = !1, this.activeTab = "inventory" },// <!-- B UI L T BY A B D U R R A H M A N -->
        exportCSV() {
            if (!this.inventoryAssets.length) return alert("No data to export.");
            const e = this.inventoryAssets.map((e => `${e.tag},${e.type},${e.brand},${e.model},${e.serial},${e.status},${e.repairCount||0},${this.calculateTotalRepairCost(e)}`)).join("\n"),
                t = new Blob(["\uFEFFAsset Tag,Type,Brand,Model,Serial Number,Status,Repair Quantity,Repair Cost\n" + e], { type: "text/csv;charset=utf-8;" }),
                s = window.URL.createObjectURL(t),
                r = document.createElement("a");
            r.href = s, r.download = `AssetCare_Inventory_${(new Date).toISOString().split("T")[0]}.csv`, r.click()
        },
        exportMonthlyCSV() { this.exportFilteredMonthlyCSV(); }
    }
}).mount("#app");

// <!-- B UI L T BY A B D U R R A H M A N -->