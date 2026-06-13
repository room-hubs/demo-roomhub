export const useDashboard = () => {
  const isNotificationsSlideoverOpen = ref(false)

  return {
    isNotificationsSlideoverOpen,
  }
}