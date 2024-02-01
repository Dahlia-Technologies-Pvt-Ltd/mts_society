import React, { useState, useEffect } from "react";
import { useSelector, useDispatch } from "@src/store/Store";
import {
    TextField,
    Avatar,
    Divider,
    IconButton,
    Stack,
    Tooltip,
    useTheme,
} from "@mui/material";
import {
    Grid,
    FormControlLabel,
    Button,
    RadioGroup,
    Typography,
    FormGroup,
    Switch,
    Box,
    Paper,
    Alert,
    AlertTitle,
} from "@mui/material";
import Snackbar from "@mui/material/Snackbar";
import { useParams, Link, useNavigate } from "react-router-dom";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import {
    isEdit,
    UpdateContact,
    DeleteContact,
    toggleStarredContact,
} from "@src/store/apps/contacts/ContactSlice";
import { Portal } from '@mui/base';
import { ContactType } from "@src/types/apps/contact";
import {
    IconPencil,
    IconStar,
    IconTrash,
    IconDeviceFloppy,
} from "@tabler/icons";
import Scrollbar from "@src/components/custom-scroll/Scrollbar";
import emailIcon from "@src/assets/images/breadcrumb/emailSv.png";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import CopyAllTwoToneIcon from '@mui/icons-material/CopyAllTwoTone';

const ContactDetails = (props) => {
    const renderHTML = (rawHTML: string) =>
        React.createElement("div", {
            dangerouslySetInnerHTML: { __html: rawHTML },
        });

    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState("");

    const [isSuccessVisible1, setIsSuccessVisible1] = useState(false);
    const [successMessage1, setSuccessMessage1] = useState("");

    const [variables, setVariables] = useState("");
    const [code, setCode] = useState("");
    const [title, setTitle] = useState("");

    const [quillText1, setQuillText1] = useState("");
    const [quillText, setQuillText] = useState("");
    const theme = useTheme();
    const borderColor = theme.palette.divider;

    const validationSchema = yup.object().shape({
        subject: yup.string().required("Subject is required"),
        content: yup.string().required(" "+"Content is required"),
        content_footer: yup.string(),
    });

    const id = props.id;

    const formik = useFormik({
        initialValues: {
            subject: "",
            content: "",
            content_footer : "",
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("subject", values.subject);
                formData.append("content", values.content);
                formData.append("template_code", code);
                formData.append("id", id);
                formData.append("title", title);
                formData.append("template_variable", variables);
                formData.append("content_footer", values.content_footer)

                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-emailtemplate";

                // Make the API POST request
                const token = localStorage.getItem("authToken");
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });
                //console.log('API Response:', response.data);
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
                fetchData();
            } catch (error) {
                // Handle errors here
                if (error.response.data.success == false) {
                    setIsErrorVisible(true);
                    setErrorMessage(error.response.data.message.subject);
                }
                console.error("API Error:", error);
            }
        },
    });

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        fetchData();
    }, [id]);

    const onCopyClicked = (item) => {
        navigator.clipboard.writeText(item);
        setSuccessMessage1("Variable Copied");
        setIsSuccessVisible1(true);
    }

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-emailtemplate/${id}`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            setVariables(data.template_variable);
            setCode(data.template_code);
            setTitle(data.title);
            const x = data.content_footer === null ? "" : data.content_footer;
            formik.setValues({
                subject: data.subject,
                content: data.content,
                content_footer: x,
            });
            setQuillText(data.content);
            setQuillText1(x);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const myArray = variables.split(",");
    var arr = [];
    {
        myArray.map((x) => arr.push("["+x.split('"')[1]+"]"));
    }

    return (
        <>
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isErrorVisible}
                autoHideDuration={3000}
                onClose={() => setIsErrorVisible(false)}
            >
                <Alert severity="error">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {errorMessage && <div>{errorMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isSuccessVisible}
                autoHideDuration={3000}
                onClose={() => setIsSuccessVisible(false)}
            >
                <Alert severity="success">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {successMessage && <div>{successMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isSuccessVisible1}
                autoHideDuration={3000}
                onClose={() => setIsSuccessVisible1(false)}
            >
                <Alert severity="success">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {successMessage1 && <div>{successMessage1}</div>}
                    </div>
                </Alert>
            </Snackbar></Portal>
            {/* ------------------------------------------- */}
            {/* Contact Detail Part */}
            {/* ------------------------------------------- */}
            <>
                {/* ------------------------------------------- */}
                {/* Header Part */}
                {/* ------------------------------------------- */}
                <Box p={3} py={2} display={"flex"} alignItems="center">
                    <Typography variant="h5">{title}</Typography>
                </Box>
                <Divider />
                {/* ------------------------------------------- */}
                {/* Contact Table Part */}
                {/* ------------------------------------------- */}
                <Box sx={{ overflow: "auto" }}>
                <Box  p={3} py={2}>
                    <Grid container>
                    {arr.map((item) => (
                        <Grid
                            item
                            xs={12}
                            md={4}
                            display="flex"
                            alignItems="center"
                            sx={{ overflow: 'hidden' }}
                        >
                        <Typography fontSize={'11px'}>
                                            {item}
                                        </Typography>
                                        <Tooltip title="Copy Variable">
                                        <IconButton aria-label="delete" onClick={() => onCopyClicked(item)}>
                                        <CopyAllTwoToneIcon fontSize="small" /></IconButton>
                                        </Tooltip>
                        </Grid>
                          ))}
                    </Grid>
                </Box>
                </Box>
                <Divider />

                <Box sx={{ padding: "20px" }}>
                    <form onSubmit={formik.handleSubmit}>
                        <Grid container>
                            {/* 1 */}
                            <Grid
                                item
                                xs={12}
                                display="flex"
                                alignItems="center"
                            >
                                <CustomFormLabel
                                    htmlFor="subject"
                                    sx={{ mt: 0 }}
                                >
                                    Email Subject
                                </CustomFormLabel>
                            </Grid>
                            <Grid item xs={12} mt={1}>
                                <CustomTextField
                                    id="subject"
                                    name="subject"
                                    placeholder="Email Subject"
                                    fullWidth
                                    value={formik.values.subject}
                                    onChange={formik.handleChange}
                                    error={
                                        formik.touched.subject &&
                                        Boolean(formik.errors.subject)
                                    }
                                    helperText={
                                        formik.touched.subject &&
                                        formik.errors.subject
                                    }
                                />
                            </Grid>

                            {/* 2 */}
                            <Grid
                                item
                                xs={12}
                                display="flex"
                                alignItems="center"
                            >
                                <CustomFormLabel htmlFor="content">
                                    Template Content
                                </CustomFormLabel>
                            </Grid>
                            <Grid item xs={12} mt={1}>
                                <Paper
                                    sx={{ border: `1px solid ${borderColor}` }}
                                    variant="outlined"
                                >
                                    <ReactQuill
                                        value={quillText}
                                        onChange={(value) => {
                                            setQuillText(value);
                                            formik.setFieldValue(
                                                "content",
                                                value
                                            );
                                        }}
                                        placeholder="Type here... ( max 255 characters )"
                                    />
                                </Paper>
                                {formik.touched.content &&
                                    formik.errors.content && (
                                        <Typography
                                            variant="caption"
                                            color="error"
                                        >
                                            {formik.errors.content}
                                        </Typography>
                                    )}
                            </Grid>

                            {/* 3 */}
                            <Grid
                                item
                                xs={12}
                                display="flex"
                                alignItems="center"
                            >
                                <CustomFormLabel htmlFor="content_footer">
                                    Footer Content
                                </CustomFormLabel>
                            </Grid>
                            <Grid item xs={12} mt={1}>
                                <Paper
                                    sx={{ border: `1px solid ${borderColor}` }}
                                    variant="outlined"
                                >
                                    <ReactQuill
                                        value={quillText1}
                                        onChange={(value) => {
                                            setQuillText1(value);
                                            formik.setFieldValue(
                                                "content_footer",
                                                value
                                            );
                                        }}
                                        placeholder="Type here... ( max 255 characters )"
                                    />
                                </Paper>
                                {formik.touched.content_footer &&
                                    formik.errors.content_footer && (
                                        <Typography
                                            variant="caption"
                                            color="error"
                                        >
                                            {formik.errors.content_footer}
                                        </Typography>
                                    )}
                            </Grid>

                            {/* Submit Button */}
                            <Grid item xs={12} mt={5} display={'flex'} alignItems={'centre'} justifyContent={'center'}>
                                <Button
                                    variant="contained"
                                    color="primary"
                                    type="submit"
                                >
                                    Update
                                </Button>
                            </Grid>
                        </Grid>
                    </form>
                </Box>
            </>
        </>
    );
};

export default ContactDetails;
