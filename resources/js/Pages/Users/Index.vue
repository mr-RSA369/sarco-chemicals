<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'

const users = ref([])
const loading = ref(false)

const loadUsers = async () => {
    try {
        loading.value = true

        const { data } = await axios.get('/api/users')

        users.value = data
    }
    finally {
        loading.value = false
    }
}

const updateRole = async (user) => {
    try {

        await axios.patch(
            `/api/users/${user.id}/role`,
            {
                role: user.role
            }
        )

    } catch (error) {

        alert(
            error.response?.data?.message ??
            'Unable to update role.'
        )

        loadUsers()
    }
}

const deleteUser = async (user) => {

    if (!confirm(`Delete ${user.name}?`))
        return

    try {

        await axios.delete(`/api/users/${user.id}`)

        loadUsers()

    } catch (error) {

        alert(
            error.response?.data?.message ??
            'Unable to delete user.'
        )
    }
}

const formatDate = (date) =>
    new Date(date).toLocaleDateString()

onMounted(loadUsers)
</script>

<template>

<AppLayout>

<div class="p-6">

    <div class="mb-6 flex justify-between items-center">

        <h1 class="text-2xl font-semibold">
            Users
        </h1>

    </div>

    <div
        class="bg-white rounded-lg shadow overflow-auto max-h-[75vh]"
    >

        <table class="min-w-full text-sm">

            <thead
                class="sticky top-0 bg-gray-100"
            >

                <tr>

                    <th class="p-3 text-left">
                        Name
                    </th>

                    <th class="p-3 text-left">
                        Email
                    </th>

                    <th class="p-3 text-center">
                        Role
                    </th>

                    <th class="p-3 text-center">
                        Joined
                    </th>

                    <th class="p-3 text-center">
                        Delete
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="user in users"
                    :key="user.id"
                    class="border-b hover:bg-gray-50"
                >

                    <td class="p-3">
                        {{ user.name }}
                    </td>

                    <td class="p-3">
                        {{ user.email }}
                    </td>

                    <td class="p-3 text-center">

                        <select
                            v-model="user.role"
                            @change="updateRole(user)"
                            class="w-full border rounded px-2 py-1"
                        >
                            <option value="user">
                                User
                            </option>

                            <option value="admin">
                                Admin
                            </option>

                            <option value="manager">
                                Manager
                            </option>

                        </select>

                    </td>

                    <td class="p-3 text-center">
                        {{ formatDate(user.created_at) }}
                    </td>

                    <td class="p-3 text-center">

                        <button
                            @click="deleteUser(user)"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                        >
                            Delete
                        </button>

                    </td>

                </tr>

                <tr v-if="!loading && users.length === 0">

                    <td
                        colspan="5"
                        class="text-center p-6 text-gray-500"
                    >
                        No Users Found
                    </td>

                </tr>

                <tr v-if="loading">

                    <td
                        colspan="5"
                        class="text-center p-6"
                    >
                        Loading...
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</AppLayout>

</template>
