<template>
    <div class="bulk-sms-container">
        <div class="header">
            <h2>Bulk SMS</h2>
        </div>

        <div class="filters-section">
            <div class="filter-group">
                <label>Filter by:</label>
                <select v-model="filters.buero" @change="filterUsers" style="width:180px">
                    <option value="">All Bueros</option>
                    <option v-for="buero in uniqueBueros" :key="buero" :value="buero">
                        {{ buero }}
                    </option>
                </select>

                <select v-model="filters.rank" @change="filterUsers" style="width:180px">
                    <option value="">All Ranks</option>
                    <option v-for="rank in uniqueRanks" :key="rank" :value="rank">
                        {{ rank }}
                    </option>
                </select>
            </div>

            <div class="search-group">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search by name..."
                    @input="filterUsers"
                >
            </div>
        </div>

        <div class="sms-composer">
      <textarea
          v-model="smsText"
          placeholder="Type your SMS message here..."
          rows="4"
      ></textarea>
            <div class="character-count">
                {{ smsText.length }} / 160 characters
            </div>
        </div>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                <tr>
                    <th>
                        <input
                            type="checkbox"
                            v-model="selectAll"
                            @change="toggleSelectAll"
                        >
                    </th>
                    <th @click="sortBy('name')">
                        Name
                        <span v-if="sortField === 'name'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
                    </th>
                    <th @click="sortBy('bueroName')">
                        Buero
                        <span v-if="sortField === 'bueroName'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
                    </th>
                    <th @click="sortBy('rankName')">
                        Rank
                        <span v-if="sortField === 'rankName'">
                {{ sortDirection === 'asc' ? '↑' : '↓' }}
              </span>
                    </th>
                    <th>Phone</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                    <td>
                        <input
                            type="checkbox"
                            v-model="selectedUsers"
                            :value="user.id"
                        >
                    </td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.bueroName || 'N/A' }}</td>
                    <td>{{ user.rankName || 'N/A' }}</td>
                    <td>{{ user.phone }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="action-buttons">
            <button
                class="send-button"
                @click="sendBulkSms"
                :disabled="!canSend"
            >
                Send SMS ({{ selectedUsers.length }})
            </button>
        </div>

        <div v-if="loading" class="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Sending SMS...</div>
        </div>

        <div v-if="successMessage" class="success-message">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            users: [],
            filteredUsers: [],
            selectedUsers: [],
            selectAll: false,
            smsText: '',
            searchQuery: '',
            sortField: 'name',
            sortDirection: 'asc',
            filters: {
                buero: '',
                rank: ''
            },
            loading: false,
            successMessage: '',
            errorMessage: ''
        }
    },

    computed: {
        canSend() {
            return this.selectedUsers.length > 0 && this.smsText.trim().length > 0;
        },

        uniqueBueros() {
            const bueros = new Set();
            this.users.forEach(user => {
                if (user.bueroName) bueros.add(user.bueroName);
            });
            return Array.from(bueros).sort();
        },

        uniqueRanks() {
            const ranks = new Set();
            this.users.forEach(user => {
                if (user.rankName) ranks.add(user.rankName);
            });
            return Array.from(ranks).sort();
        }
    },

    async created() {
        await this.fetchUsers();
        this.filterUsers();
    },

    methods: {
        async fetchUsers() {
            try {
                const response = await axios.get('/api/active-members');
                // Assuming the API now returns bueroName and rankName with each user
                this.users = response.data;
            } catch (error) {
                console.error('Error fetching users:', error);
                this.errorMessage = 'Failed to load users';
            }
        },

        filterUsers() {
            let filtered = [...this.users];

            // Apply search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(user =>
                    user.name.toLowerCase().includes(query)
                );
            }

            // Apply buero filter
            if (this.filters.buero) {
                filtered = filtered.filter(user =>
                    user.bueroName === this.filters.buero
                );
            }

            // Apply rank filter
            if (this.filters.rank) {
                filtered = filtered.filter(user =>
                    user.rankName === this.filters.rank
                );
            }

            // Apply sorting
            filtered.sort((a, b) => {
                let fieldA = a[this.sortField];
                let fieldB = b[this.sortField];

                if (fieldA < fieldB) return this.sortDirection === 'asc' ? -1 : 1;
                if (fieldA > fieldB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });

            this.filteredUsers = filtered;
            this.selectAll = false;
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.filterUsers();
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedUsers = this.filteredUsers.map(user => user.id);
            } else {
                this.selectedUsers = [];
            }
        },

        async sendBulkSms() {
            if (!this.canSend) return;

            this.loading = true;
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const response = await axios.post('/api/send-bulk-sms', {
                    user_ids: this.selectedUsers,
                    message: this.smsText
                });

                this.successMessage = `SMS sent successfully to users.`;
                this.smsText = '';
                this.selectedUsers = [];
                this.selectAll = false;
            } catch (error) {
                console.error('Error sending bulk SMS:', error);
                this.errorMessage = 'Failed to send SMS to some users. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.bulk-sms-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.header {
    margin-bottom: 20px;
}

.filters-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-group, .search-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: bold;
}

select, input[type="text"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.sms-composer {
    margin-bottom: 20px;
}

.sms-composer textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.character-count {
    text-align: right;
    font-size: 0.8em;
    color: #666;
}

.users-table-container {
    overflow-x: auto;
    margin-bottom: 20px;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th, .users-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.users-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    cursor: pointer;
}

.users-table th:hover {
    background-color: #e9e9e9;
}

.users-table tr:hover {
    background-color: #f9f9f9;
}

.action-buttons {
    text-align: right;
}

.send-button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.send-button:hover {
    background-color: #45a049;
}

.send-button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loading-spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: white;
    margin-top: 15px;
    font-size: 18px;
}

.success-message {
    color: #4CAF50;
    margin-top: 15px;
    padding: 10px;
    background-color: #f8fff8;
    border: 1px solid #4CAF50;
    border-radius: 4px;
}

.error-message {
    color: #f44336;
    margin-top: 15px;
    padding: 10px;
    background-color: #fff8f8;
    border: 1px solid #f44336;
    border-radius: 4px;
}
</style>
