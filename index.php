<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssetCare - Enterprise Management</title>
    <link rel="icon" type="image/png" href="logo.png">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<!-- B UI L T BY A B D U R R A H M A N -->
<body class="bg-[#F5F5F7] font-sans text-gray-900 overflow-x-hidden">
    <div id="app" class="min-h-screen flex flex-col relative">
        <div v-if="isInitialLoading"
            class="fixed inset-0 bg-[#F5F5F7] z-[9999] flex flex-col items-center justify-center transition-opacity duration-500">
            <i class="fas fa-circle-notch fa-spin text-5xl text-blue-500 mb-6 drop-shadow-md"></i>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">AssetCare</h1>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-widest animate-pulse">Establishing Secure
                Connection...</p>
        </div>

        <!-- Month Details Popup / Modal -->
        <div v-if="showMonthDetailModal"
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4 animate-pop">
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 border border-gray-100 relative"><button
                    @click="closeMonthDetailModal"
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-700 transition-colors"><i
                        class="fas fa-times text-xl"></i></button>
                <div class="flex items-center gap-3 mb-6 text-blue-600">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center"><i
                            class="fas fa-file-invoice-dollar text-2xl text-blue-500"></i></div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            {{ selectedMonthDetail.monthName }} {{ selectedMonthDetail.year }} - Parts Summary</h2>
                            <!-- B UI L T BY A B D U R R A H M A N -->
                        <p class="text-xs text-gray-500">Summary of repair items, replacement quantities, and total
                            costs. Click any item row to view asset details.</p>
                    </div>
                </div>
                <div
                    class="max-h-80 overflow-y-auto bg-gray-50 rounded-2xl border border-gray-200 p-2 mb-6 shadow-inner">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase text-gray-400 border-b border-gray-200">
                                <th class="p-3 w-12 text-center">#</th>
                                <th class="p-3">Item / Description</th>
                                <th class="p-3 text-center">Quantity</th>
                                <th class="p-3 text-right">Total Amount</th>
                            </tr>
                        </thead><!-- B UI L T BY A B D U R R A H M A N -->
                        <tbody>
                            <tr v-for="(item, idx) in selectedMonthDetail.items" :key="idx"
                                @click="openItemDetails(item)"
                                class="border-b border-gray-100 last:border-0 hover:bg-blue-50/50 cursor-pointer transition-colors group">
                                <td class="p-3 text-xs font-bold text-gray-400 text-center">{{ idx + 1 }}</td>
                                <td class="p-3 text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <span>{{ item.item }}</span><i
                                        class="fas fa-external-link-alt text-xs text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </td>
                                <td class="p-3 text-center text-sm font-bold text-blue-600">{{ item.quantity }}</td>
                                <td class="p-3 text-right text-sm font-bold text-emerald-600">
                                    Tk {{ item.amount.toLocaleString() }}</td>
                            </tr>
                            <tr v-if="!selectedMonthDetail.items || selectedMonthDetail.items.length===0">
                                <td colspan="4" class="p-6 text-center text-gray-400 text-sm">No specific parts logged
                                    for this month.</td>
                            </tr><!-- B UI L T BY A B D U R R A H M A N -->
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <button @click="exportMonthDetailsCSV"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-3 rounded-xl smooth-btn flex items-center gap-2 text-sm shadow-md">
                        <i class="fas fa-file-csv"></i>Download
                    </button>
                    <button @click="closeMonthDetailModal"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-3 rounded-xl smooth-btn text-sm">Close</button>
                </div>
            </div>
        </div>

        <!-- Item Details Popup / Modal -->
        <div v-if="showItemDetailModal"
            class="fixed inset-0 bg-gray-900/65 backdrop-blur-sm z-[110] flex items-center justify-center p-4 animate-pop"><!-- B UI L T BY A B D U R R A H M A N -->
            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 border border-gray-100 relative"><button
                    @click="closeItemDetailModal"
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-700 transition-colors"><i
                        class="fas fa-times text-xl"></i></button>
                <div class="flex items-center gap-3 mb-6 text-indigo-600">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center"><i
                            class="fas fa-microchip text-2xl text-indigo-500"></i></div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight capitalize">
                            {{ selectedItemDetail.itemName }} Replacement Summary</h2>
                        <p class="text-xs text-gray-500">{{ selectedItemDetail.monthName }} {{ selectedItemDetail.year }}
                            &middot; Asset replacement and cost breakdown</p>
                    </div>
                </div>
                <div
                    class="max-h-80 overflow-y-auto bg-gray-50 rounded-2xl border border-gray-200 p-2 mb-6 shadow-inner">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase text-gray-400 border-b border-gray-200">
                                <th class="p-3 w-12 text-center">#</th>
                                <th class="p-3">Asset Tag</th>
                                <th class="p-3 text-center">Quantity</th>
                                <th class="p-3 text-right">Cost Amount</th>
                            </tr>
                        </thead>
                        <tbody><!-- B UI L T BY A B D U R R A H M A N -->
                            <tr v-for="(asset, idx) in selectedItemDetail.assets" :key="idx"
                                class="border-b border-gray-100 last:border-0 hover:bg-white transition-colors">
                                <td class="p-3 text-xs font-bold text-gray-400 text-center">{{ idx + 1 }}</td>
                                <td class="p-3 text-sm font-bold text-gray-800 font-mono"><i
                                        class="fas fa-tag text-blue-400 mr-2 text-xs"></i>{{ asset.tag }}</td>
                                <td class="p-3 text-center text-sm font-bold text-blue-600">{{ asset.quantity }}</td>
                                <td class="p-3 text-right text-sm font-bold text-emerald-600">
                                    Tk {{ asset.cost.toLocaleString() }}</td>
                            </tr>
                            <tr v-if="!selectedItemDetail.assets || selectedItemDetail.assets.length===0">
                                <td colspan="4" class="p-6 text-center text-gray-400 text-sm">No replacement assets
                                    found for this category.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <button @click="exportItemDetailsCSV"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-3 rounded-xl smooth-btn flex items-center gap-2 text-sm shadow-md">
                        <i class="fas fa-file-csv"></i>Download
                    </button>
                    <button @click="closeItemDetailModal"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-3 rounded-xl smooth-btn text-sm">Back
                        / Close</button>
                </div>
            </div>
        </div>
<!-- B UI L T BY A B D U R R A H M A N -->
        <div v-if="showBulkDuplicateModal"
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4 animate-pop">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 border border-gray-100 relative"><button
                    @click="closeDuplicateModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors"><i
                        class="fas fa-times text-xl"></i></button>
                <div class="flex items-center gap-3 mb-6 text-orange-500"><i
                        class="fas fa-exclamation-triangle text-3xl"></i>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Duplicate Assets Blocked</h2>
                </div>
                <p class="text-sm text-gray-600 mb-4">The smart detection system skipped the following
                    <b>{{ bulkDuplicates.length }}</b> assets because their Tag or Serial Number already exists in the
                    inventory. All other valid assets were successfully imported.</p>
                <div
                    class="max-h-60 overflow-y-auto bg-gray-50 rounded-xl border border-gray-200 p-2 mb-6 shadow-inner">
                    <div v-for="dup in bulkDuplicates" :key="dup.tag"
                        class="p-3 border-b border-gray-100 last:border-0 flex justify-between items-center text-sm">
                        <span class="font-bold text-gray-800"><i
                                class="fas fa-tag text-gray-400 mr-2 text-xs"></i>{{ dup.tag }}</span><span
                            class="font-mono text-xs text-gray-500">SN:{{ dup.serial }}</span></div>
                </div><button @click="closeDuplicateModal"
                    class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn">Understood, View
                    Inventory</button>
            </div>
        </div><!-- B UI L T BY A B D U R R A H M A N -->

        <div v-else-if="!currentUser"
            class="min-h-screen flex flex-col items-center justify-center bg-[#F5F5F7] p-4 relative">
            <div
                class="bg-white p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] w-full max-w-md border border-gray-100 animate-pop hover-lift z-10">
                <div class="text-center mb-8"><i
                        class="fas fa-shield-alt text-4xl text-blue-500 mb-3 hover:rotate-12 transition-transform duration-500"></i>
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">AssetCare</h1>
                </div>

                <!-- LOGIN VIEW -->
                <div v-if="authView==='login'" class="space-y-4 animate-pop" style="animation-delay: 0.1s;"><input
                        v-model="authForm.username" @keyup.enter="login" type="text" placeholder="Username"
                        class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"><!-- B UI L T BY A B D U R R A H M A N -->
                    <div class="relative w-full"><input v-model="authForm.password" @keyup.enter="login"
                            :type="showPassword ? 'text' : 'password'" placeholder="Password"
                            class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm pr-10"><button
                            type="button" @click="showPassword=!showPassword"
                            class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 focus:outline-none"><i
                                :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i></button></div><button
                        @click="login"
                        class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Sign
                        In</button>
                    <div class="flex justify-between mt-4"><button @click="authView='forgot'"
                            class="text-sm text-blue-500 hover:text-blue-600 transition-colors">Forgot
                            Password?</button></div>
                </div>

                <!-- FIRST TIME FORCED PASSWORD RESET VIEW -->
                <div v-if="authView==='force_reset'" class="space-y-4 animate-pop">
                    <div class="p-3.5 bg-amber-50 border border-amber-200/80 rounded-2xl text-amber-800 text-xs flex items-start gap-3">
                        <i class="fas fa-key text-amber-500 text-base mt-0.5"></i>
                        <div>
                            <p class="font-bold">First-Time Login Required Action</p><!-- B UI L T BY A B D U R R A H M A N -->
                            <p class="text-[11px] text-amber-700 mt-0.5">Your account was created by an administrator. Security policy requires you to reset your password before accessing the portal.</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">New Password (Min. 8 Chars)</label>
                        <input v-model="firstLoginForm.newPassword" @keyup.enter="completeFirstLoginPasswordReset" type="password" placeholder="Enter new password"
                            class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm mt-1">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Confirm New Password</label>
                        <input v-model="firstLoginForm.confirmPassword" @keyup.enter="completeFirstLoginPasswordReset" type="password" placeholder="Confirm new password"
                            class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm mt-1">
                    </div>
                    <button @click="completeFirstLoginPasswordReset"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-xl smooth-btn mt-2 shadow-md">
                        Reset Password & Enter Portal
                    </button>
                </div>
<!-- B UI L T BY A B D U R R A H M A N -->
                <!-- FORGOT PASSWORD VIEW -->
                <div v-if="authView==='forgot'" class="space-y-4 animate-pop">
                    <p class="text-sm text-gray-500 mb-2">Enter your credentials to verify your identity.</p><input
                        v-model="forgotForm.username" @keyup.enter="verifyForgotIdentity" type="text"
                        placeholder="Your Username"
                        class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"><input
                        v-model="forgotForm.email" @keyup.enter="verifyForgotIdentity" type="email"
                        placeholder="Your Registered Email"
                        class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"><button
                        @click="verifyForgotIdentity"
                        class="w-full bg-gray-900 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Verify
                        Identity</button><button @click="authView='login'"
                        class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Back to
                        Login</button>
                </div>

                <!-- VERIFY SECURITY CODE VIEW -->
                <div v-if="authView==='verify_code'" class="space-y-4 animate-pop">
                    <div class="p-3.5 bg-blue-50 border border-blue-200/80 rounded-2xl text-blue-800 text-xs flex items-start gap-3"><!-- B UI L T BY A B D U R R A H M A N -->
                        <i class="fas fa-envelope text-blue-500 text-base mt-0.5"></i>
                        <div>
                            <p class="font-bold">Security Check</p>
                            <p class="text-[11px] text-blue-700 mt-0.5">We have sent a 6-digit verification code to your registered email address.</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">6-Digit Code</label>
                        <input v-model="recoveryCodeInput" @keyup.enter="submitRecoveryCode" type="text" placeholder="XXXXXX" maxlength="6"
                            class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-lg text-center tracking-[0.5em] font-bold mt-1">
                    </div>
                    <button @click="submitRecoveryCode"
                        class="w-full bg-gray-900 hover:bg-black text-white font-medium py-3 rounded-xl smooth-btn mt-2 shadow-md">
                        Verify Identity
                    </button>
                    <button @click="authView='login'"
                        class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Cancel / Back to Login</button><!-- B UI L T BY A B D U R R A H M A N -->
                </div>

                <!-- RESET PASSWORD VIEW -->
                <div v-if="authView==='reset'" class="space-y-4 animate-pop">
                    <p class="text-sm text-green-600 font-medium mb-2"><i class="fas fa-check-circle mr-1"></i>Identity
                        verified. Enter your new password.</p><input v-model="forgotForm.newPassword"
                        @keyup.enter="resetPassword" type="password" placeholder="New Password"
                        class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"><input
                        v-model="forgotForm.confirmPassword" @keyup.enter="resetPassword" type="password"
                        placeholder="Confirm New Password"
                        class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"><button
                        @click="resetPassword"
                        class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Save New
                        Password</button><button @click="authView='login'"
                        class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Cancel</button><!-- B UI L T BY A B D U R R A H M A N -->
                </div>

                <div v-if="authMessage" class="mt-6 p-3 text-sm text-center rounded-xl font-medium animate-pop"
                    :class="messageColor">{{ authMessage }}</div>
            </div>
            <div class="absolute bottom-8 w-full text-center z-0">
                <p class="uppercase text-[#8b95a6] tracking-widest text-sm drop-shadow-md font-semibold">BUILT BY ABDUR
                    RAHMAN</p>
            </div>
        </div>

        <div v-else class="flex-grow flex flex-col animate-pop">
            <div class="sticky top-0 z-50 p-4 w-full bg-[#F5F5F7]/70 backdrop-blur-md"><!-- B UI L T BY A B D U R R A H M A N -->
                <nav
                    class="max-w-7xl mx-auto glass-nav-premium rounded-3xl shadow-[0_8px_32px_rgba(0,0,0,0.06)] text-gray-900 p-2 flex flex-wrap justify-between items-center transition-all duration-500 hover:shadow-[0_12px_48px_rgba(0,0,0,0.09)]">
                    <div class="flex items-center gap-3 pl-2 cursor-pointer group">
                        <div
                            class="w-11 h-11 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-105 group-hover:rotate-3 transition-all duration-400">
                            <i class="fas fa-laptop-medical text-white text-xl"></i></div>
                        <div class="flex flex-col justify-center hidden sm:flex">
                            <h1 class="text-[17px] font-bold tracking-tight text-gray-900 leading-tight">AssetCare</h1>
                            <span
                                class="text-[9px] font-black uppercase tracking-widest text-blue-600 leading-none">{{ currentUser.role }}</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center p-1.5 bg-gray-100/60 rounded-2xl border border-gray-200/50 overflow-x-auto no-scrollbar">
                        <button @click="activeTab='dashboard'" :class="tabClass('dashboard')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-chart-pie"
                                :class="activeTab==='dashboard' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Dashboard</span></button>
                        
                        <button v-if="['admin', 'Maintenance', 'IT Support'].includes(currentUser.role)" @click="activeTab='inventory'"
                            :class="tabClass('inventory')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-boxes"
                                :class="activeTab==='inventory' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Repair Inventory</span></button>
                                
                        <button v-if="['admin', 'Maintenance', 'IT Support', 'Management'].includes(currentUser.role)" @click="activeTab='slotDetails'" :class="tabClass('slotDetails')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-list-ol"
                                :class="activeTab==='slotDetails' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Slot Details</span></button>
                        <button v-if="currentUser.role === 'Maintenance'" @click="activeTab='accessories'"
                            :class="tabClass('accessories')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                            <i class="fas fa-keyboard" :class="activeTab==='accessories' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><!-- B UI L T BY A B D U R R A H M A N -->
                            <span class="hidden xl:inline">Accessories</span></button>
                        <button v-if="['admin', 'Management'].includes(currentUser.role)" @click="activeTab='monthlyReport'"
                            :class="tabClass('monthlyReport')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-calendar-alt"
                                :class="activeTab==='monthlyReport' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Monthly Report</span></button>
                                
                        <button v-if="['admin', 'Maintenance'].includes(currentUser.role)" @click="activeTab='add'"
                            :class="tabClass('add')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-plus-circle"
                                :class="activeTab==='add' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Add Device</span></button>
                                
                        <button @click="activeTab='profile'"
                            :class="tabClass('profile')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-user-circle"
                                :class="activeTab==='profile' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Profile</span></button>
                                
                        <button v-if="currentUser.role==='admin'" @click="activeTab='adminPanel'"
                            :class="tabClass('adminPanel')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-shield-alt"
                                :class="activeTab==='adminPanel' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Admin</span><span
                                v-if="(pendingUsers.length >0 || pendingAssets.length >0) && currentUser.role==='admin'"
                                class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white animate-pulse shadow-sm border border-white">{{ pendingUsers.length + pendingAssets.length }}</span></button>
                                
                        <button v-if="['admin', 'Maintenance'].includes(currentUser.role)" @click="activeTab='bulk'"
                            :class="tabClass('bulk')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none"><i
                                class="fas fa-layer-group"
                                :class="activeTab==='bulk' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span
                                class="hidden xl:inline">Bulk</span></button></div>
                    <div class="pr-2 flex items-center gap-3"><button @click="logout"
                            class="w-11 h-11 flex items-center justify-center rounded-2xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 hover:shadow-lg hover:shadow-red-500/30 group outline-none"><i
                                class="fas fa-power-off text-lg group-hover:scale-110 transition-transform"></i></button>
                    </div>
                </nav>
            </div><!-- B UI L T BY A B D U R R A H M A N -->

            <main class="flex-grow p-4 md:p-6 max-w-7xl mx-auto w-full relative">
                <!-- Dashboard Tab -->
                <div v-if="activeTab==='dashboard'" class="space-y-8 animate-pop">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">Operational Overview</h2>
                            <p class="text-gray-500 text-sm mt-1">Real-time statistics and asset monitoring.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">System Time</p>
                            <p class="text-sm font-medium text-gray-800">{{ new Date().toLocaleDateString() }}</p>
                        </div>
                    </div><!-- B UI L T BY A B D U R R A H M A N -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                            <div class="flex justify-between items-start mb-4"><i class="fas fa-boxes text-2xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Repair Inventory</span></div>
                            <p class="text-4xl font-semibold mb-1 tracking-tight">{{ inventoryAssets.length }}</p>
                            <p class="text-xs font-medium text-gray-500">Total Registered Assets</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                            <div class="flex justify-between items-start mb-4"><i class="fas fa-tools text-2xl text-orange-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-orange-50 text-orange-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Live</span></div>
                            <p class="text-4xl font-semibold mb-1 tracking-tight">{{ inRepairCount }}</p>
                            <p class="text-xs font-medium text-gray-500">Currently in Repair Cell</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                            <div class="flex justify-between items-start mb-4"><i class="fas fa-history text-2xl text-emerald-500 group-hover:rotate-12 transition-transform duration-300"></i><span class="text-[10px] bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Success</span></div>
                            <p class="text-4xl font-semibold mb-1 tracking-tight">{{ allTimeRepairedCount }}</p>
                            <p class="text-xs font-medium text-gray-500">Total Repaired Devices</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                            <div class="flex justify-between items-start mb-4"><i class="fas fa-truck text-2xl text-teal-500 group-hover:translate-x-2 transition-transform duration-300"></i><span class="text-[10px] bg-teal-50 text-teal-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Delivered</span></div>
                            <p class="text-4xl font-semibold mb-1 tracking-tight">{{ deliveredCount }}</p><!-- B UI L T BY A B D U R R A H M A N -->
                            <p class="text-xs font-medium text-gray-500">Delivered to IT</p>
                        </div>
                        <div v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                            <div class="flex justify-between items-start mb-4"><i class="fas fa-user-check text-2xl text-purple-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-purple-50 text-purple-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Users</span></div>
                            <p class="text-4xl font-semibold mb-1 tracking-tight">{{ totalActiveUserCount }}</p>
                            <p class="text-xs font-medium text-gray-500">Total Active Users</p>
                        </div>
                    </div><!-- B UI L T BY A B D U R R A H M A N -->
                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Status Breakdown</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                            <div v-for="st in ['In Assessment', 'Quick Repair Stage', 'Complex Stage', 'Ready', 'Irreparable', 'EOL/ Disposed']" :key="st" class="relative">
                                <p class="text-[10px] font-bold uppercase text-gray-500 mb-1 tracking-wider">{{ st }}</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ statusCounts[st] || 0 }}</p>
                                <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out" :class="statusBg(st)" :style="{ width: ( (statusCounts[st] || 0) / (inventoryAssets.length || 1) * 100) + '%'}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- B UI L T BY A B D U R R A H M A N -->

                <!-- Inventory Tab -->
                <div v-if="activeTab==='inventory'" class="space-y-6 animate-pop">
                    <div class="glass-nav-premium p-4 rounded-2xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4 sticky top-[100px] z-40">
                        <div class="flex items-center gap-3 bg-white/60 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                            <i class="fas fa-search text-gray-400"></i><input v-model="searchQuery" type="text" placeholder="Search Tag or Serial..." class="w-full outline-none bg-transparent text-sm">
                        </div>
                        <div class="flex items-center gap-3 bg-white/60 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                            <i class="fas fa-filter text-gray-400"></i>
                            <select v-model="statusFilter" class="w-full bg-transparent outline-none text-sm text-gray-700">
                                <option value="">All Statuses</option>
                                <option v-for="st in statusOptions" :value="st">{{ st }}</option>
                            </select><!-- B UI L T BY A B D U R R A H M A N -->
                        </div>
                    </div>
                    <div v-for="asset in paginatedAssets" :key="asset.tag" class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden mb-4 hover-lift">
                        <div class="p-5 bg-gray-50/50 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex-grow">
                                <div class="flex items-center gap-3">
                                    <span class="bg-gray-200/70 text-gray-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ asset.type || 'Asset' }}</span>
                                    <h2 class="text-lg font-semibold text-gray-900 tracking-tight">{{ asset.tag }} &middot; <span class="font-normal text-gray-600">{{ asset.brand }} {{ asset.model }}</span></h2>
                                </div>
                                <div class="flex gap-4 mt-2 items-center">
                                    <p class="text-xs font-mono text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-100">SN:{{ asset.serial }}</p>
                                    <p class="text-[10px] text-orange-600 font-bold uppercase tracking-wider">Age:{{ calculateAge(asset.purchaseDate) }}</p>
                                    <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded-md">Total Cost: Tk {{ calculateTotalRepairCost(asset) }}</p>
                                    <p v-if="asset.repairCount" class="text-[10px] text-blue-600 font-bold uppercase tracking-wider bg-blue-50 px-2 py-0.5 rounded-md">Repaired:{{ asset.repairCount }} times</p>
                                </div><!-- B UI L T BY A B D U R R A H M A N -->
                            </div>
                            <div class="flex items-center gap-3">
                                <select v-if="['admin', 'Maintenance'].includes(currentUser.role)" :value="asset.status" @change="confirmStatusChange($event, asset)" class="p-2.5 rounded-xl font-medium text-xs border outline-none cursor-pointer transition-colors" :class="statusColor(asset.status)">
                                    <option v-for="st in statusOptions" :value="st">{{ st }}</option>
                                </select>
                                <span v-else class="p-2.5 rounded-xl font-medium text-xs border cursor-default" :class="statusColor(asset.status)">{{ asset.status }}</span>
                                <button v-if="currentUser.role==='admin'" @click="deleteAsset(asset.tag)" class="text-red-400 hover:text-red-600 p-2 transition-transform duration-300 hover:scale-110"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-5 grid md:grid-cols-2 gap-8">
                            <div class="text-sm">
                                <p class="font-bold mb-3 text-gray-400 uppercase tracking-widest text-[10px]">Technical History</p>
                                <div class="max-h-48 overflow-y-auto space-y-3 pr-2 smooth-scroll">
                                    <template v-for="rep in (asset.repairs || []).slice().reverse()">
                                        <div class="bg-gray-50/50 p-3 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden transition-all hover:bg-gray-100">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400"></div>
                                            <div class="flex justify-between font-medium text-gray-800 ml-2">
                                                <span>{{ rep.problem }} 
                                                    <span v-if="rep.repSerial || rep.accDate || rep.warrantyMonths" class="block text-xs text-gray-500 font-normal mt-0.5">
                                                        <span v-if="rep.repSerial"><i class="fas fa-barcode mr-1"></i>S/N:{{ rep.repSerial }}</span>
                                                        <span v-if="rep.accDate" :class="rep.repSerial ? 'ml-3' : ''" class="text-indigo-500 font-medium"><i class="fas fa-calendar-alt mr-1"></i>Acc. Date: {{ rep.accDate }}</span>
                                                        <span v-if="rep.warrantyMonths" :class="rep.repSerial || rep.accDate ? 'ml-3' : ''" class="text-orange-500 font-medium"><i class="fas fa-shield-alt mr-1"></i>Warranty: {{ rep.warrantyMonths }}</span>
                                                    </span>
                                                </span><!-- B UI L T BY A B D U R R A H M A N -->
                                                <span class="text-green-600 font-semibold">Tk {{ rep.cost }}</span>
                                            </div>
                                            <p class="text-right text-[10px] text-gray-400 mt-1 font-medium">{{ rep.date }}</p>
                                        </div>
                                    </template>
                                    <p v-if="!asset.repairs || !asset.repairs.length" class="text-gray-400 text-sm py-4 text-center">No history documented yet.</p>
                                </div>
                            </div>
                            <div v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="bg-white p-5 rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgb(0,0,0,0.02)] space-y-3">
                                <p class="font-bold mb-1 text-gray-400 uppercase tracking-widest text-[10px]">Log Repair / Replacement</p>
                                <input v-model="getDraft(asset.tag).problem" @keyup.enter="addRepair(asset.tag)" placeholder="Repaired Item Description" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                                <div class="flex gap-3">
                                    <input v-model="getDraft(asset.tag).repSerial" @keyup.enter="addRepair(asset.tag)" placeholder="Replacement Part S/N" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                                    <input v-model="getDraft(asset.tag).accDate" @keyup.enter="addRepair(asset.tag)" type="date" title="Accessories Purchase Date" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-gray-600">
                                </div><!-- B UI L T BY A B D U R R A H M A N -->
                                <div class="flex gap-3">
                                    <input v-model="getDraft(asset.tag).cost" @keyup.enter="addRepair(asset.tag)" type="number" placeholder="Repair Cost (Tk)" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                                    
                                    <input v-model="getDraft(asset.tag).warrantyMonths" @keyup.enter="addRepair(asset.tag)" type="number" placeholder="Warranty (Months)" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                                    
                                    <input v-model="getDraft(asset.tag).date" @keyup.enter="addRepair(asset.tag)" type="date" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-gray-600">
                                </div>
                                <button @click="addRepair(asset.tag)" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl text-sm smooth-btn mt-2">Update Asset History</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="filteredAssets.length >0" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm animate-pop">
                        <div class="text-sm text-gray-500 font-medium">Showing <span class="font-bold text-gray-800">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to <span class="font-bold text-gray-800">{{ Math.min(currentPage * itemsPerPage, filteredAssets.length) }}</span> of <span class="font-bold text-gray-800">{{ filteredAssets.length }}</span> assets </div>
                        <div class="flex items-center gap-1.5"><button @click="currentPage=1" :disabled="currentPage===1" class="w-9 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 disabled:opacity-40 disabled:pointer-events-none smooth-btn text-xs hover:bg-gray-100 hover:text-blue-600"><i class="fas fa-angle-double-left"></i></button><button @click="currentPage--" :disabled="currentPage===1" class="px-3 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-xs font-semibold disabled:opacity-40 disabled:pointer-events-none smooth-btn hover:bg-gray-100 hover:text-blue-600">Prev</button><span class="text-xs font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 mx-1">Page {{ currentPage }} of {{ totalPages }} </span><button @click="currentPage++" :disabled="currentPage >=totalPages" class="px-3 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-xs font-semibold disabled:opacity-40 disabled:pointer-events-none smooth-btn hover:bg-gray-100 hover:text-blue-600">Next</button><button @click="currentPage=totalPages" :disabled="currentPage >=totalPages" class="w-9 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 disabled:opacity-40 disabled:pointer-events-none smooth-btn text-xs hover:bg-gray-100 hover:text-blue-600"><i class="fas fa-angle-double-right"></i></button></div>
                    </div>
                </div>

                <!-- Slot Details Tab --><!-- B UI L T BY A B D U R R A H M A N -->
                <div v-if="activeTab==='slotDetails'" class="space-y-6 animate-pop">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">Repair Slot Details</h2>
                            <p class="text-gray-500 text-sm mt-1">Manage and track bulk repair slot assignments.</p>
                        </div>
                    </div>
                    <div v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="fas fa-plus-square text-blue-500"></i>Create New Slot Record</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">S/N</label><input v-model="slotForm.sn" @keyup.enter="submitSlot" type="text" placeholder="Serial No" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Date</label><input v-model="slotForm.date_val" @keyup.enter="submitSlot" type="date" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm text-gray-600"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Slot No</label><input v-model="slotForm.slotNo" @keyup.enter="submitSlot" type="text" placeholder="Ex: S-01" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Slot Name</label><input v-model="slotForm.slotName" @keyup.enter="submitSlot" type="text" placeholder="Ex: Laptop/Desktop Repair" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Total Assets</label><input v-model="slotForm.totalAssets" @keyup.enter="submitSlot" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Return to IT</label><input v-model="slotForm.returnToIT" @keyup.enter="submitSlot" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">EOL / Disposed</label><input v-model="slotForm.eol" @keyup.enter="submitSlot" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Pending</label><input v-model="slotForm.pending" @keyup.enter="submitSlot" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                            <div class="col-span-2 md:col-span-4"><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Remarks</label><input v-model="slotForm.remarks" @keyup.enter="submitSlot" type="text" placeholder="Notes" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        </div>
                        <div class="flex justify-end gap-3 mt-4"><button @click="clearSlotForm" class="bg-gray-100 text-gray-600 font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm hover:bg-gray-200">Clear</button><button @click="submitSlot" class="bg-blue-500 text-white font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm shadow-md">Submit Slot Record</button></div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden"><!-- B UI L T BY A B D U R R A H M A N -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-max">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                        <th class="p-4">S/N</th>
                                        <th class="p-4">Date</th>
                                        <th class="p-4">Slot No</th>
                                        <th class="p-4">Slot Name</th>
                                        <th class="p-4 text-center">Total Assets</th>
                                        <th class="p-4 text-center">Return to IT</th>
                                        <th class="p-4 text-center">EOL</th>
                                        <th class="p-4 text-center">Pending</th>
                                        <th class="p-4">Remarks</th>
                                        <th v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="p-4 text-center">Action</th>
                                    </tr>
                                </thead><!-- B UI L T BY A BD UR RA HM AN -->
                                <tbody>
                                    <tr v-for="slot in slots" :key="slot.id" class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                        <template v-if="editingSlotId===slot.id">
                                            <td class="p-2"><input v-model="slot.sn" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-semibold text-gray-800 p-1"></td>
                                            <td class="p-2"><input v-model="slot.date_val" type="date" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-xs text-gray-500 p-1"></td>
                                            <td class="p-2"><input v-model="slot.slotNo" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-bold text-blue-600 p-1"></td>
                                            <td class="p-2"><input v-model="slot.slotName" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-medium text-gray-700 p-1"></td>
                                            <td class="p-2"><input v-model="slot.totalAssets" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm p-1"></td>
                                            <td class="p-2"><input v-model="slot.returnToIT" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-teal-600 font-semibold p-1"></td>
                                            <td class="p-2"><input v-model="slot.eol" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-gray-500 font-semibold p-1"></td>
                                            <td class="p-2"><input v-model="slot.pending" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-orange-500 font-semibold p-1"></td>
                                            <td class="p-2"><input v-model="slot.remarks" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-xs text-gray-500 p-1"></td>
                                            <td class="p-2 text-center flex justify-center gap-2 mt-1"><button @click="saveSlotInline(slot)" class="text-green-500 hover:text-green-700 p-1 transition-transform hover:scale-110" title="Save"><i class="fas fa-save"></i></button><button @click="cancelEdit()" class="text-gray-400 hover:text-gray-600 p-1 transition-transform hover:scale-110" title="Cancel"><i class="fas fa-times"></i></button></td>
                                        </template>
                                        <template v-else>
                                            <td class="p-4 text-sm font-semibold text-gray-800">{{ slot.sn }}</td>
                                            <td class="p-4 text-xs text-gray-500">{{ slot.date_val }}</td>
                                            <td class="p-4 text-sm font-bold text-blue-600">{{ slot.slotNo }}</td>
                                            <td class="p-4 text-sm font-medium text-gray-700">{{ slot.slotName || 'N/A' }}</td>
                                            <td class="p-4 text-center text-sm">{{ slot.totalAssets }}</td>
                                            <td class="p-4 text-center text-sm text-teal-600 font-semibold">{{ slot.returnToIT }}</td>
                                            <td class="p-4 text-center text-sm text-gray-500 font-semibold">{{ slot.eol }}</td>
                                            <td class="p-4 text-center text-sm text-orange-500 font-semibold">{{ slot.pending }}</td>
                                            <td class="p-4 text-xs text-gray-500">{{ slot.remarks }}</td>
                                            <td v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="p-4 text-center flex justify-center gap-2"><button @click="editSlot(slot.id)" class="text-blue-500 hover:text-blue-700 p-1 transition-transform hover:scale-110" title="Edit"><i class="fas fa-edit"></i></button><button @click="deleteSlot(slot.id)" class="text-red-400 hover:text-red-600 p-1 transition-transform hover:scale-110" title="Delete"><i class="fas fa-trash"></i></button></td>
                                        </template>
                                    </tr>
                                    <tr v-if="slots.length===0">
                                        <td colspan="10" class="p-8 text-center text-gray-400 text-sm">No slot records found.</td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="slots.length >0" class="bg-blue-50/50 border-t-2 border-blue-100 font-bold text-sm text-gray-800">
                                    <tr>
                                        <td colspan="4" class="p-4 text-right uppercase tracking-wider text-[11px] text-gray-500">Overall Totals:</td>
                                        <td class="p-4 text-center">{{ slotTotals.totalAssets }}</td>
                                        <td class="p-4 text-center text-teal-700">{{ slotTotals.returnToIT }}</td>
                                        <td class="p-4 text-center text-gray-700">{{ slotTotals.eol }}</td>
                                        <td class="p-4 text-center text-orange-600">{{ slotTotals.pending }}</td>
                                        <td class="p-4"></td>
                                        <td v-if="['admin', 'Maintenance'].includes(currentUser.role)" class="p-4"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!-- B UI L T BY A B D U R R A H M A N -->

                <!-- Accessories Search Tab -->
                <div v-if="activeTab === 'accessories'" class="space-y-6 animate-pop">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">Accessories Replacement History</h2>
                            <p class="text-gray-500 text-sm mt-1">Scan or manually enter an accessory serial number to view its history.</p>
                        </div>
                    </div>
                    
                    <!-- Search Bar Container --><!-- B UI L T BY A B D U R R A H M A N -->
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="flex-grow flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                                <i class="fas fa-barcode text-gray-400"></i>
                                <input v-model="accessoriesSearchQuery" @keyup.enter="searchAccessories" type="text" placeholder="Scan barcode or enter serial number..." class="w-full outline-none bg-transparent text-sm" autofocus>
                            </div>
                            <button @click="searchAccessories" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl smooth-btn shadow-md whitespace-nowrap">
                                Go
                            </button>
                            <button @click="clearAccessoriesSearch" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-3 rounded-xl smooth-btn whitespace-nowrap">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Search Results View -->
                    <div v-if="accessoriesSearchResults.length > 0" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-max">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                        <th class="p-4 rounded-tl-lg">Type</th>
                                        <th class="p-4">Serial</th>
                                        <th class="p-4">Asset Tag</th>
                                        <th class="p-4">Warranty</th>
                                        <th class="p-4 rounded-tr-lg">Purchase Date</th>
                                    </tr>
                                </thead><!-- B UI L T BY A B D U R R A H M A N -->
                                <tbody>
                                    <tr v-for="(res, idx) in accessoriesSearchResults" :key="idx" class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                        <td class="p-4 text-sm font-semibold text-gray-800 capitalize">{{ res.type }}</td>
                                        <td class="p-4 text-sm font-mono text-gray-600">{{ res.serial }}</td>
                                        <td class="p-4 text-sm font-bold text-blue-600">{{ res.assetTag }}</td>
                                        <td class="p-4 text-sm text-gray-700">{{ res.warranty }}</td>
                                        <td class="p-4 text-sm text-gray-700">{{ res.purchaseDate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- No Results State -->
                    <div v-else-if="accessoriesSearchQuery && accessoriesSearchResults.length === 0" class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-center">
                        <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                        <h3 class="text-lg font-medium text-gray-700">No results found</h3>
                        <p class="text-sm text-gray-500 mt-1">No accessory history matches the serial number entered.</p>
                    </div>
                </div>
<!-- B UI L T BY A B D U R R A H M A N -->
                <!-- Monthly Report Tab -->
                <div v-if="activeTab==='monthlyReport' && ['admin', 'Management'].includes(currentUser.role)" class="space-y-6 animate-pop">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">Monthly Report Overview</h2>
                            <p class="text-gray-500 text-sm mt-1">Review statistical analysis and monthly repair costs.</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 flex flex-wrap justify-center items-end gap-4 hover-lift">
                        <div class="flex flex-col w-44">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">Category / Part</label>
                            <select v-model="reportSelectCategory" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                                <option value="All">All Categories</option>
                                <option v-for="cat in availableCategories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div><!-- B UI L T BY A B D U R R A H M A N -->
                        <div class="flex flex-col w-40">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">From (Month)</label>
                            <select v-model="reportSelectMonthFrom" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                                <option value="All">All Months</option>
                                <option value="1">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
                            </select>
                        </div>
                        <div class="flex flex-col w-40">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">To (Month)</label>
                            <select v-model="reportSelectMonthTo" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                                <option value="All">All Months</option>
                                <option value="1">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
                            </select>
                        </div>
                        <div class="flex flex-col w-40">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">Select Year</label>
                            <select v-model="reportSelectYear" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                                <option value="All">All Years</option>
                                <option v-for="y in [2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032]" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button @click="applyMonthlyFilter" class="bg-blue-500 text-white font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm shadow-md h-[42px]">Go</button>
                            <button @click="clearMonthlyFilter" class="bg-gray-100 text-gray-600 font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm hover:bg-gray-200 h-[42px]">Clear</button>
                            <button @click="exportFilteredMonthlyCSV" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl smooth-btn text-sm shadow-md h-[42px] flex items-center gap-2">
                                <i class="fas fa-file-csv"></i>Download
                            </button>
                        </div><!-- B UI L T BY A B D U R R A H M A N -->
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden hover-lift">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-max">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                        <th class="p-4 w-16 text-center">#</th>
                                        <th class="p-4">Month & Year</th>
                                        <th class="p-4 text-center">Total Repaired</th>
                                        <th class="p-4 text-center">Unique Assets Repaired</th>
                                        <th class="p-4 text-right">Total Monthly Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in monthlyReportData" :key="row.key" @click="openMonthDetails(row)" class="border-b border-gray-50 hover:bg-blue-50/50 cursor-pointer transition-colors group">
                                        <td class="p-4 text-center text-xs font-bold text-gray-400">{{ idx + 1 }}</td>
                                        <td class="p-4 text-sm font-semibold text-gray-800">{{ row.monthName }} {{ row.year }}</td>
                                        <td class="p-4 text-center text-sm font-bold text-blue-600">{{ row.assetCount }}</td>
                                        <td class="p-4 text-center text-sm font-bold text-indigo-600">{{ row.uniqueAssetCount }}</td>
                                        <td class="p-4 text-right text-sm font-bold text-emerald-600">Tk {{ row.cost.toLocaleString() }}</td>
                                    </tr>
                                    <tr v-if="!monthlyReportData || monthlyReportData.length === 0">
                                        <td colspan="5" class="p-8 text-center text-gray-400 text-sm">No monthly repair records found for the selected filters.</td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="isMonthlyFilterActive" class="bg-emerald-50/50 border-t-2 border-emerald-200 font-bold text-sm text-gray-900">
                                    <tr>
                                        <td colspan="4" class="p-4 text-right uppercase tracking-wider text-[11px] text-gray-600">Total Calculated Cost (Based on Filter):</td>
                                        <td class="p-4 text-right text-emerald-700 text-base">Tk {{ totalFilteredMonthlyCost.toLocaleString() }}</td>
                                    </tr>
                                </tfoot>
                            </table><!-- B UI L T BY A B D U R R A H M A N -->
                        </div>
                    </div>
                </div>

                <!-- Add Device Tab -->
                <div v-if="activeTab==='add' && ['admin', 'Maintenance'].includes(currentUser.role)" class="max-w-xl mx-auto space-y-6 animate-pop">
                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i>Register New Asset</h2>
                        <div class="space-y-4">
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Asset Tag</label><input v-model="singleForm.tag" type="text" placeholder="Tag Number" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Asset Type</label><input v-model="singleForm.type" type="text" placeholder="Ex: Laptop, Desktop" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                                <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Brand</label><input v-model="singleForm.brand" type="text" placeholder="Ex: Dell, HP" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Model</label><input v-model="singleForm.model" type="text" placeholder="Model Name/ Number" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                                <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Serial Number</label><input v-model="singleForm.serial" type="text" placeholder="Serial No" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            </div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Purchase Date</label><input v-model="singleForm.purchaseDate" type="date" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium text-gray-600"></div>
                            <p v-if="duplicateAssetWarning" class="text-xs text-red-500 font-bold mt-2"><i class="fas fa-exclamation-triangle mr-1"></i>Warning: Asset Tag or Serial Number already exists!</p>
                            <button @click="addSingleDevice" :disabled="duplicateAssetWarning" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn text-sm shadow-md mt-4 disabled:opacity-50">Submit New Asset</button>
                        </div>
                    </div>
                </div>
<!-- B UI L T BY A B D U R R A H M A N -->
                <!-- Profile Tab -->
                <div v-if="activeTab==='profile'" class="max-w-2xl mx-auto space-y-6 animate-pop">
                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl font-bold"><i class="fas fa-user-shield"></i></div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ currentUser.username }}</h2>
                                <p class="text-xs text-gray-500">{{ currentUser.email }} &middot; <span class="text-blue-600 font-bold uppercase">{{ currentUser.role }}</span></p>
                            </div>
                        </div>
                        <div class="space-y-6 border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Security & Password Management</h3>
                            <div class="space-y-3">
                                <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Current Password</label><input v-model="profileUpdate.currentPass" type="password" placeholder="Enter Current Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">New Password (Min 8 Chars)</label><input v-model="profileUpdate.newPass" type="password" placeholder="New Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                                    <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Confirm New Password</label><input v-model="profileUpdate.confirmPass" type="password" placeholder="Confirm Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                                </div>
                                <button @click="changePassword" class="bg-blue-500 text-white font-medium px-6 py-3 rounded-xl smooth-btn text-sm shadow-md mt-2">Update Password</button>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-4">Email Configuration</h3>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">New Email (@quantanite.com)</label><input v-model="profileUpdate.newEmail" type="email" placeholder="username@quantanite.com" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                                    <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Confirm New Email</label><input v-model="profileUpdate.confirmEmail" type="email" placeholder="Confirm Email" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                                </div>
                                <button @click="changeEmail" class="bg-gray-800 text-white font-medium px-6 py-3 rounded-xl smooth-btn text-sm shadow-md mt-2">Update Email Address</button>
                            </div>
                        </div>
                    </div>
                </div><!-- B UI L T BY A B D U R R A H M A N -->

                <!-- Admin Panel Tab -->
                <div v-if="activeTab==='adminPanel' && currentUser.role==='admin'" class="space-y-8 animate-pop">
                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight mb-6 flex items-center gap-2"><i class="fas fa-user-plus text-blue-500"></i>Create New User Role</h2>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Username</label><input v-model="adminAddUserForm.username" type="text" placeholder="Username" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Email (@quantanite.com)</label><input v-model="adminAddUserForm.email" type="email" placeholder="user@quantanite.com" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Temporary Password (Min 8 Chars)</label><input v-model="adminAddUserForm.password" type="password" placeholder="Min 8 characters" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium"></div>
                            <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">User Role</label>
                                <select v-model="adminAddUserForm.role" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm font-medium text-gray-700">
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="IT Support">IT Support</option>
                                    <option value="Management">Management</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-blue-600 font-medium"><i class="fas fa-info-circle mr-1"></i>New users will be forced to change this password on their first login.</p>
                            <button @click="adminCreateUser" class="bg-blue-500 text-white font-medium px-8 py-3 rounded-xl smooth-btn text-sm shadow-md">Create User Account</button>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow border hover-lift">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 border-b pb-2"><i class="fas fa-chart-line text-purple-600"></i> Reporting & Data Export</h2>
                        <p class="text-sm text-gray-500 mb-6">Export full system inventory data for audits and external reporting.</p>
                            <div class="flex flex-wrap gap-4">
                                <button @click="exportCSV" class="bg-green-600 text-white font-bold py-3 px-6 rounded shadow flex items-center gap-2 smooth-btn"><i class="fas fa-file-excel"></i> Export Inventory (CSV)</button>
                                <button @click="exportMonthlyCSV" class="bg-emerald-600 text-white font-bold py-3 px-6 rounded shadow flex items-center gap-2 smooth-btn"><i class="fas fa-file-csv"></i> Export Monthly Report (CSV)</button>
                            </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight mb-6 flex items-center gap-2"><i class="fas fa-users-cog text-blue-500"></i>User Role & Status Management</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-max">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                        <th class="p-4">User</th>
                                        <th class="p-4">Email</th>
                                        <th class="p-4">Assigned Role</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4 text-center">First Login Security</th>
                                        <th class="p-4 text-center">Actions</th>
                                    </tr>
                                </thead><!-- B UI L T BY A B D U R R A H M A N -->
                                <tbody>
                                    <tr v-for="u in users" :key="u.username" class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                        <td class="p-6 font-semibold text-sm text-gray-800 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full" :class="isOnline(u) ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-gray-300'"></span>
                                            {{ u.username }}
                                        </td>
                                        <td class="p-4 text-xs text-gray-500">{{ u.email }}</td>
                                        <td class="p-4">
                                            <select :value="u.role" @change="changeUserRole(u.username, $event.target.value, $event)" :disabled="u.username==='admin'" class="p-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold outline-none cursor-pointer">
                                                <option value="Maintenance">Maintenance</option>
                                                <option value="IT Support">IT Support</option>
                                                <option value="Management">Management</option>
                                                <option value="admin">Administrator</option>
                                            </select>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider" :class="u.status==='blocked' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'">{{ u.status }}</span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span v-if="parseInt(u.mustResetPassword, 10)===1" class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-[10px] font-bold"><i class="fas fa-lock mr-1"></i>Reset Required</span>
                                            <span v-else class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-medium"><i class="fas fa-check mr-1"></i>Verified</span>
                                        </td>
                                        <td class="p-4 text-center flex justify-center gap-2">
                                            <button v-if="u.username!=='admin'" @click="toggleUserBlock(u.username)" class="p-2 rounded-xl text-xs font-semibold transition-colors" :class="u.status==='blocked' ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-orange-50 text-orange-600 hover:bg-orange-100'">
                                                {{ u.status==='blocked' ? 'Unblock' : 'Block' }}
                                            </button>
                                            <button v-if="u.username!=='admin'" @click="deleteUser(u.username)" class="text-red-400 hover:text-red-600 p-2 transition-transform hover:scale-110"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- B UI L T BY A B D U R R A H M A N -->

                <!-- Bulk Import Tab -->
                <div v-if="activeTab==='bulk' && ['admin', 'Maintenance'].includes(currentUser.role)" class="max-w-xl mx-auto space-y-6 animate-pop">
                    <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-center hover-lift">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4"><i class="fas fa-file-csv"></i></div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight mb-2">Bulk Asset Import</h2>
                        <p class="text-xs text-gray-500 mb-6">Upload a CSV file containing asset inventory records. Duplicates will be automatically detected and safely filtered out. Table: Asset Tag, Type, Brand, Model, Serial, Purchase Date.</p>
                        
                        <div v-if="isBulkProcessing" class="py-8">
                            <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500 mb-3"></i>
                            <p class="text-sm font-semibold text-gray-700">Processing Inventory CSV...</p>
                        </div>
                        <div v-else class="relative border-2 border-dashed border-gray-200 rounded-2xl p-8 hover:border-blue-400 transition-colors">
                            <input type="file" @change="handleFileUpload" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Click or drag CSV file here to upload</p><!-- B UI L T BY A B D U R R A H M A N -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="app.js"></script>
</body>

</html>