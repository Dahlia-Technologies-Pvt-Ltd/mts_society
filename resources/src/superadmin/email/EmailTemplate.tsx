import React, { useEffect, useState } from "react";
import { Button, Box, Drawer, useMediaQuery, Theme } from "@mui/material";
import {
    ListItemText,
    Avatar,
    ListItemButton,
    Typography,
    Stack,
    ListItemAvatar,
    useTheme,
} from "@mui/material";
import ChildCard from "@src/components/shared/ChildCard";
import {
    Grid,
    Accordion,
    AccordionSummary,
    AccordionDetails,
    Divider,
} from "@mui/material";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import { useSelector, useDispatch } from "@src/store/Store";
import axios from "axios";
import ContactDetails from "@src/superadmin/email/ContactDetails";
import ContactList from "@src/components/apps/contacts/ContactList";
import ContactSearch from "@src/components/apps/contacts/ContactSearch";
import ContactFilter from "@src/components/apps/contacts/ContactFilter";
import AppCard from "@src/components/shared/AppCard";
import ParentCard from "@src/components/shared/ParentCard";

import { IconChevronDown } from '@tabler/icons';

const drawerWidth = 240;
const secdrawerWidth = 320;

const EmailTemplate = () => {

    const BCrumb = [
        {
            to: "/super-admin/dashboard",
            title: "Home",
        },
        {
            title: "Email Template",
        },
    ];


    const [page, setPage] = useState("");
    const [totalCount, setTotalCount] = useState("");
    const [keyword, setkeyword] = useState("");
    const [loading, setLoading] = useState(true);

    const [datas, setData] = useState([]); // State to store the fetched data
    useEffect(() => {
        //when the component mounts
        fetchData();
    }, [setData]);

    // Function to fetch data from the API
    const fetchData = async (perPage = "", page = "") => {
        if (page == "") {
            setPage("1");
        }
        if (perPage == "") {
            perPage = "10";
            sessionStorage.removeItem("searchKeyword");
        }
        try {
            const formData = new FormData();
            formData.append("page", page);
            formData.append("sortBy", "id");
            formData.append("sortOrder", "ASC");
            formData.append("perPage", "");
            const searchKeyword = sessionStorage.getItem("searchKeyword");
            formData.append(
                "keyword",
                searchKeyword !== null ? searchKeyword : ""
            );
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = appUrl + "/api/list-emailtemplate";
            const token = localStorage.getItem("authToken");
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            if (response && response.data && response.data.data) {
                setData(response.data.data.data);
                setTotalCount(response.data.data.total);
                console.log(response.data.data.data);
            } else {
                setData(response.data);
                setTotalCount(0);
                console.error("Error: Unexpected response structure", response);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        } finally {
            setLoading(false);
        }
    };

    const [isLeftSidebarOpen, setLeftSidebarOpen] = useState(false);
    const [active, setActive] = useState(1);
    const [isRightSidebarOpen, setRightSidebarOpen] = useState(false);
    const lgUp = useMediaQuery((theme: Theme) => theme.breakpoints.up("lg"));
    const mdUp = useMediaQuery((theme: Theme) => theme.breakpoints.up("md"));

    const clickFunction = (id) => {
        setActive(id);
    };

    return (
        <PageContainer
            title="Email Template"
            description="Email Template"
        >
            <Breadcrumb title="" items={BCrumb}/>
            <AppCard>
                {/* ------------------------------------------- */}
                {/* Middle part */}
                {/* ------------------------------------------- */}
                <Box
                    sx={{
                        minWidth: secdrawerWidth,
                        width: {
                            xs: "100%",
                            md: secdrawerWidth,
                            lg: secdrawerWidth,
                        },
                        flexShrink: 0,
                    }}
                >
                  
                    {datas.map((item) => (
                        <ListItemButton
                            sx={{ mb: 1 }}
                            selected={active === item.id}
                        >
                            <ListItemText
                                onClick={() => clickFunction(item.id)}
                            >
                                <Stack
                                    direction="row"
                                    gap="10px"
                                    alignItems="center"
                                >
                                    <Box mr="auto">
                                        <Typography
                                            variant="subtitle1"
                                            fontWeight={600}
                                        >
                                            {item.title}
                                        </Typography>
                                        <Typography
                                            variant="body2"
                                            color="text.secondary"
                                            
                                        >
                                            {item.subject}
                                        </Typography>
                                    </Box>
                                </Stack>
                            </ListItemText>
                        </ListItemButton>
                    ))}
                </Box>

                {/* ------------------------------------------- */}
                {/* Right part */}
                {/* ------------------------------------------- */}
                <Drawer
                    anchor="right"
                    open={isRightSidebarOpen}
                    onClose={() => setRightSidebarOpen(false)}
                    variant={mdUp ? "permanent" : "temporary"}
                    sx={{
                        width: mdUp ? secdrawerWidth : "100%",
                        zIndex: lgUp ? 0 : 1,
                        flex: mdUp ? "auto" : "",
                        [`& .MuiDrawer-paper`]: {
                            width: "100%",
                            position: "relative",
                        },
                    }}
                >
                    {/* back btn Part */}
                    {mdUp ? (
                        ""
                    ) : (
                        <Box sx={{ p: 3 }}>
                            <Button
                                variant="outlined"
                                color="primary"
                                size="small"
                                onClick={() => setRightSidebarOpen(false)}
                                sx={{
                                    mb: 3,
                                    display: {
                                        xs: "block",
                                        md: "none",
                                        lg: "none",
                                    },
                                }}
                            >
                                Back{" "}
                            </Button>
                        </Box>
                    )}
                    <ContactDetails id={active} />
                </Drawer>
            </AppCard>
        </PageContainer>
    );
};

export default EmailTemplate;
