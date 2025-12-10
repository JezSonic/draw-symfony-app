import type { CreateDrawPayload, Draw } from '@/types/draw'
import { get, post } from './http'

export async function listDraws(): Promise<Draw[]> {
  return get<Draw[]>('/draws')
}

export async function getDraw(id: string): Promise<Draw> {
  return get<Draw>(`/draws/${encodeURIComponent(id)}`)
}

export async function createDraw(payload: CreateDrawPayload): Promise<Draw> {
  return post<Draw>('/draws', payload)
}

export async function runDraw(id: string): Promise<Draw> {
  return post<Draw>(`/draws/${encodeURIComponent(id)}/run`)
}
