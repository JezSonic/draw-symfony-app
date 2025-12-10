export const API_BASE: string = (import.meta.env.VITE_API_URL as string | undefined) || '/api'

function join(base: string, path: string): string {
  const b = base.endsWith('/') ? base.slice(0, -1) : base
  return path.startsWith('/') ? b + path : `${b}/${path}`
}

function hasCookie(name: string): boolean {
  if (typeof document === 'undefined') return false
  const n = `${encodeURIComponent(name)}=`
  return document.cookie.split(';').some((c) => c.trim().startsWith(n))
}

export async function ensureXsrfCookie(): Promise<void> {
  // Check for XSRF cookie and fetch it if missing
  if (hasCookie('XSRF-TOKEN')) return
  try {
    // This endpoint is expected at the app root, not under API_BASE
    await fetch(join(API_BASE, '/csrf/token'), {
      method: 'GET',
      credentials: 'include',
      headers: {
        Accept: 'application/json',
      },
    })
  } catch {
    // Silently ignore; downstream request will surface any real errors
  }
}

async function handle<T>(res: Response): Promise<T> {
  const text = await res.text()
  const maybeJson = text ? safeJson(text) : null
  if (!res.ok) {
    const message = (maybeJson as any)?.message || (maybeJson as any)?.detail || res.statusText || 'Request failed'
    throw new Error(message)
  }
  return (maybeJson as T) as T
}

function safeJson(text: string) {
  try {
    return JSON.parse(text)
  } catch {
    return null
  }
}

export async function get<T>(path: string, init?: RequestInit): Promise<T> {
  await ensureXsrfCookie()
  const res = await fetch(join(API_BASE, path), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
    },
    credentials: 'include',
    ...init,
  })
  return handle<T>(res)
}

export async function post<T>(path: string, body?: unknown, init?: RequestInit): Promise<T> {
  await ensureXsrfCookie()
  const res = await fetch(join(API_BASE, path), {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    body: body === undefined ? undefined : JSON.stringify(body),
    credentials: 'include',
    ...init,
  })
  return handle<T>(res)
}
