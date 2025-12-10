export type DrawStatus = 'IN_PROGRESS' | 'FINISHED'

export interface DrawOption {
  id: string
  content: string
  author: string
}

export interface DrawResult {
  winners: DrawOption[]
}

export interface Draw {
  id: string
  name: string
  resultsCount: number
  options: DrawOption[]
  status: DrawStatus
  result?: DrawResult
  createdAt: string
  finishedAt?: string
}

export interface CreateDrawPayload {
  name: string
  resultsCount: number
  options: Array<{ content: string; author: string }>
}
