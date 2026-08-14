<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import type { CalendarRootEmits, CalendarRootProps } from 'reka-ui'
import { useForwardPropsEmits } from 'reka-ui'
import {
    CalendarCell,
    CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell,
    CalendarHeader,
    CalendarHeading,
    CalendarNext,
    CalendarPrev,
    CalendarRoot,
} from 'reka-ui'
import { cn } from '@/lib/utils'
import { ChevronLeft, ChevronRight } from '@lucide/vue'

const props = defineProps<
    CalendarRootProps & {
        class?: HTMLAttributes['class']
        highlightedDates?: string[]
    }
>()

const emits = defineEmits<CalendarRootEmits>()

const forwarded = useForwardPropsEmits(
    {
        ...props,
        class: undefined,
        highlightedDates: undefined,
    },
    emits,
)

function isHighlighted(day: { toString: () => string }): boolean {
    if (!props.highlightedDates) return false
    return props.highlightedDates.includes(day.toString())
}
</script>

<template>
    <CalendarRoot v-slot="{ weekDays, grid }" v-bind="forwarded" :class="cn('p-3', props.class)">
        <CalendarHeader class="relative flex w-full items-center justify-between pt-1">
            <CalendarPrev
                class="inline-flex size-8 items-center justify-center rounded-md border border-border bg-transparent p-0 text-muted-foreground opacity-50 hover:opacity-100"
            >
                <ChevronLeft class="size-4" />
            </CalendarPrev>
            <CalendarHeading class="text-sm font-medium text-foreground" />
            <CalendarNext
                class="inline-flex size-8 items-center justify-center rounded-md border border-border bg-transparent p-0 text-muted-foreground opacity-50 hover:opacity-100"
            >
                <ChevronRight class="size-4" />
            </CalendarNext>
        </CalendarHeader>

        <div class="mt-4 flex flex-col gap-y-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
            <CalendarGrid
                v-for="month in grid"
                :key="month.value.toString()"
                class="w-full border-collapse space-y-1"
            >
                <CalendarGridHead>
                    <CalendarGridRow class="flex">
                        <CalendarHeadCell
                            v-for="day in weekDays"
                            :key="day"
                            class="w-9 rounded-md text-[0.8rem] font-normal text-muted-foreground"
                        >
                            {{ day }}
                        </CalendarHeadCell>
                    </CalendarGridRow>
                </CalendarGridHead>
                <CalendarGridBody>
                    <CalendarGridRow
                        v-for="(weekDates, index) in month.rows"
                        :key="`weekDate-${index}`"
                        class="mt-2 flex w-full"
                    >
                        <CalendarCell
                            v-for="weekDate in weekDates"
                            :key="weekDate.toString()"
                            :date="weekDate"
                            class="relative flex size-9 items-center justify-center p-0 text-center text-sm"
                        >
                            <CalendarCellTrigger
                                :day="weekDate"
                                :month="month.value"
                                :class="
                                    cn(
                                        'inline-flex size-9 items-center justify-center rounded-md p-0 text-sm font-normal transition-colors',
                                        'hover:bg-accent hover:text-accent-foreground',
                                        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                                        'data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:font-semibold',
                                        'data-[disabled]:text-muted-foreground/30 data-[disabled]:pointer-events-none',
                                        'data-[outside-month]:text-muted-foreground/30 data-[outside-month]:pointer-events-none',
                                        isHighlighted(weekDate)
                                            ? 'bg-primary/15 text-primary font-medium'
                                            : 'text-foreground',
                                    )
                                "
                            />
                        </CalendarCell>
                    </CalendarGridRow>
                </CalendarGridBody>
            </CalendarGrid>
        </div>
    </CalendarRoot>
</template>
