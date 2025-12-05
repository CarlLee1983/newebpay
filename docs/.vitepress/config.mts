import { defineConfig } from 'vitepress'

export default defineConfig({
    title: "NewebPay PHP SDK",
    description: "藍新金流 PHP SDK 全方位整合方案",
    lang: 'zh-TW',
    cleanUrls: true,

    head: [
        ['link', { rel: 'icon', href: '/favicon.ico' }]
    ],

    themeConfig: {
        logo: 'https://img.shields.io/badge/Neweb-Pay-blue?style=flat-square&logo=php',

        nav: [
            { text: '指南', link: '/guide/getting-started' },
            { text: 'API', link: '/api/' },
            { text: 'Changelog', link: 'https://github.com/CarlLee1983/newebpay/blob/master/CHANGELOG.md' }
        ],

        sidebar: [
            {
                text: '開始使用',
                items: [
                    { text: '簡介', link: '/guide/introduction' },
                    { text: '快速安裝', link: '/guide/installation' },
                    { text: '快速上手', link: '/guide/getting-started' }
                ]
            },
            {
                text: '核心功能',
                items: [
                    { text: '信用卡支付', link: '/guide/credit-card' },
                    { text: 'ATM / WebATM', link: '/guide/atm' },
                    { text: '超商繳費', link: '/guide/cvs' },
                    { text: '行動支付 (LINE/Taiwan Pay)', link: '/guide/mobile-pay' },
                    { text: '全功能支付 (AllInOne)', link: '/guide/all-in-one' }
                ]
            },
            {
                text: '進階主題',
                items: [
                    { text: 'Laravel 整合', link: '/guide/laravel' },
                    { text: 'Webhook 通知', link: '/guide/webhook' },
                    { text: '查詢與退款', link: '/guide/query-refund' },
                    { text: '前後端分離', link: '/guide/frontend' }
                ]
            }
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/CarlLee1983/newebpay' }
        ],

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2025 Carl Lee'
        },

        editLink: {
            pattern: 'https://github.com/CarlLee1983/newebpay/edit/master/docs/:path',
            text: '在 GitHub 上編輯此頁'
        },

        search: {
            provider: 'local'
        }
    }
})
