// utils/phone.js
export const toE164 = (phone) => {
  // Remove all spaces, dashes, parentheses
  let digits = phone.replace(/[\s\-().]/g, '')

  // Remove leading + if present
  if (digits.startsWith('+')) digits = digits.slice(1)

  // If starts with country code 855 already, use as-is
  if (digits.startsWith('855')) return `+${digits}`

  // Strip leading 0 (local format like 0969502991 → 969502991)
  if (digits.startsWith('0')) digits = digits.slice(1)

  return `+855${digits}`
}