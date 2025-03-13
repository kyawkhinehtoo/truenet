<template>
  <div>
    <h1>Backup dbbackup</h1>

    <form @submit.prevent="submit">
      <div>
        <label for="backup_email">Backup Email</label>
        <input v-model="form.backup_email" id="backup_email" type="email"
          :class="{ 'is-invalid': form.errors.backup_email }" />
        <span v-if="form.errors.backup_email" class="text-red-500">{{ form.errors.backup_email }}</span>
      </div>

      <div>
        <label for="backup_frequency">Backup Frequency</label>
        <select v-model="form.backup_frequency" id="backup_frequency">
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <button type="submit">Save dbbackup</button>
    </form>

    <button @click="triggerBackup">Backup Now</button>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';

export default {
  props: {
    dbbackup: Object,
  },
  setup(props) {
    const form = useForm({
      backup_email: props.dbbackup.backup_email || '',
      backup_frequency: props.dbbackup.backup_frequency || 'daily',
    });

    const submit = () => {
      form.post(route('dbbackup.update'));
    };

    const triggerBackup = () => {
      form.post(route('dbbackup.store'));
    };

    return {
      form,
      submit,
      triggerBackup,
    };
  },
};
</script>